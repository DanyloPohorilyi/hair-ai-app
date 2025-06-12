import torch
import torch.nn as nn
import torch.optim as optim
import torch.nn.functional as F

device = torch.device("cpu")

IMG_SIZE = 256
LATENT_DIM = 512


class DummyHairSegmentation(nn.Module):
    def __init__(self):
        super(DummyHairSegmentation, self).__init__()

    def forward(self, img):
        b, c, h, w = img.shape

        mask = torch.zeros((b, 1, h, w), device=img.device)

        radius = h // 4
        cy, cx = h // 2, w // 2
        y_coords = torch.arange(h, device=img.device).view(-1, 1).repeat(1, w)
        x_coords = torch.arange(w, device=img.device).view(1, -1).repeat(h, 1)
        dist_sq = (x_coords - cx)**2 + (y_coords - cy)**2
        circle = (dist_sq < radius**2).float()

        mask[:, 0, :, :] = circle
        return mask


def dummy_inpaint(img, mask):
    masked_img = img * mask
    return masked_img


class HairGenerator(nn.Module):
    def __init__(self, latent_dim=512, hidden_dim=256, out_channels=3, img_size=256):
        super(HairGenerator, self).__init__()
        self.latent_dim = latent_dim
        self.img_size = img_size

        self.net = nn.Sequential(
            nn.Linear(latent_dim, hidden_dim),
            nn.ReLU(True),
            nn.Linear(hidden_dim, hidden_dim *
                      (img_size // 16) * (img_size // 16)),
            nn.ReLU(True),
        )

        self.upconv = nn.Sequential(
            nn.ConvTranspose2d(hidden_dim, hidden_dim //
                               2, 4, stride=2, padding=1),
            nn.BatchNorm2d(hidden_dim // 2),
            nn.ReLU(True),
            nn.ConvTranspose2d(hidden_dim // 2, hidden_dim //
                               4, 4, stride=2, padding=1),
            nn.BatchNorm2d(hidden_dim // 4),
            nn.ReLU(True),
            nn.ConvTranspose2d(hidden_dim // 4, out_channels,
                               4, stride=2, padding=1),
            nn.Tanh()
        )

    def forward(self, latent_vector):
        x = self.net(latent_vector)
        batch_size = x.shape[0]
        h = self.img_size // 16

        hidden_dim = x.shape[1] // (h*h)
        x = x.view(batch_size, hidden_dim, h, h)

        out = self.upconv(x)
        return out


class RotateEncoder(nn.Module):
    def __init__(self, latent_dim=512):
        super(RotateEncoder, self).__init__()
        self.fc = nn.Sequential(
            nn.Linear(latent_dim*2, latent_dim),
            nn.ReLU(True),
            nn.Linear(latent_dim, latent_dim)
        )

    def forward(self, latent_src, latent_tgt_pose):

        x = torch.cat([latent_src, latent_tgt_pose], dim=1)
        return self.fc(x)


class ShapeEncoder(nn.Module):
    def __init__(self, in_channels=3, out_dim=128):
        super(ShapeEncoder, self).__init__()
        self.conv = nn.Sequential(
            nn.Conv2d(in_channels, 64, 4, 2, 1),
            nn.ReLU(True),
            nn.Conv2d(64, 128, 4, 2, 1),
            nn.ReLU(True)
        )

        self.fc = nn.Linear(128*(64*64), out_dim)

    def forward(self, x):

        h = self.conv(x)
        b, c, hh, ww = h.shape
        h = h.view(b, -1)
        out = self.fc(h)
        return out


class ShapeAdaptor(nn.Module):
    def __init__(self, shape_dim=128, face_dim=128, out_dim=64*64):
        super(ShapeAdaptor, self).__init__()
        in_dim = shape_dim + face_dim
        self.fc = nn.Sequential(
            nn.Linear(in_dim, 256),
            nn.ReLU(True),
            nn.Linear(256, out_dim)
        )

    def forward(self, shape_emb, face_emb):
        x = torch.cat([shape_emb, face_emb], dim=1)
        out = self.fc(x)

        b = out.shape[0]
        out_mask = out.view(b, 1, 64, 64)
        return out_mask


class ColorEncoder(nn.Module):
    def __init__(self, latent_dim=512):
        super(ColorEncoder, self).__init__()
        self.fc = nn.Sequential(
            nn.Linear(latent_dim*2, latent_dim),
            nn.ReLU(True),
            nn.Linear(latent_dim, latent_dim)
        )

    def forward(self, src_latent, color_latent):
        x = torch.cat([src_latent, color_latent], dim=1)
        delta = self.fc(x)
        return src_latent + delta


class RefinementEncoder(nn.Module):
    def __init__(self, latent_dim=512):
        super(RefinementEncoder, self).__init__()
        self.fc = nn.Sequential(
            nn.Linear(latent_dim*2, latent_dim),
            nn.ReLU(True),
            nn.Linear(latent_dim, latent_dim)
        )

    def forward(self, blend_latent, original_latent):
        x = torch.cat([blend_latent, original_latent], dim=1)
        return self.fc(x)


class HairFastDemo(nn.Module):
    """
    1) Pose Alignment (RotateEncoder + сегментація) 
    2) Shape Alignment (ShapeEncoder + ShapeAdaptor + inpaint)
    3) Color Alignment (ColorEncoder)
    4) Refinement Alignment (RefinementEncoder)
    5) Генератор
    """

    def __init__(self, latent_dim=512, img_size=256):
        super(HairFastDemo, self).__init__()

        self.rotate_enc = RotateEncoder(latent_dim)
        self.shape_enc = ShapeEncoder(in_channels=3, out_dim=128)
        self.shape_adaptor = ShapeAdaptor(
            shape_dim=128, face_dim=128, out_dim=64*64)
        self.color_enc = ColorEncoder(latent_dim)
        self.refine_enc = RefinementEncoder(latent_dim)

        self.generator = HairGenerator(
            latent_dim=latent_dim,
            hidden_dim=256,
            out_channels=3,
            img_size=img_size
        )

        self.segmentation = DummyHairSegmentation()

        self.register_buffer('latent_face',
                             torch.randn(1, latent_dim, device=device))
        self.register_buffer('latent_shape',
                             torch.randn(1, latent_dim, device=device))
        self.register_buffer('latent_color',
                             torch.randn(1, latent_dim, device=device))

    def forward(self, face_img, shape_img, color_img):
        b = face_img.shape[0]

        latent_face_batch = self.latent_face.repeat(b, 1)
        latent_shape_batch = self.latent_shape.repeat(b, 1)

        rotated_shape_latent = self.rotate_enc(
            latent_face_batch, latent_shape_batch)

        hairmask_face = self.segmentation(face_img)
        hairmask_shape = self.segmentation(shape_img)

        face_emb = self.shape_enc(face_img)
        shape_emb = self.shape_enc(shape_img)

        mask64 = self.shape_adaptor(shape_emb, face_emb)

        face_inpainted = dummy_inpaint(face_img, hairmask_face)

        latent_color_batch = self.latent_color.repeat(b, 1)

        blend_latent = self.color_enc(rotated_shape_latent, latent_color_batch)

        refined_latent = self.refine_enc(blend_latent, latent_face_batch)

        out_img = self.generator(refined_latent)

        return out_img, mask64, hairmask_face, hairmask_shape


def simple_train_demo(model, steps=10, lr=1e-4):

    optimizer = optim.Adam(model.parameters(), lr=lr)

    for step in range(steps):
        face_img = torch.randn(2, 3, IMG_SIZE, IMG_SIZE, device=device)
        shape_img = torch.randn(2, 3, IMG_SIZE, IMG_SIZE, device=device)
        color_img = torch.randn(2, 3, IMG_SIZE, IMG_SIZE, device=device)

        out_img, mask64, face_mask, shape_mask = model(
            face_img, shape_img, color_img)

        loss = F.mse_loss(out_img, face_img)

        optimizer.zero_grad()
        loss.backward()
        optimizer.step()

        if (step+1) % 2 == 0:
            print(f"[{step+1}/{steps}] Loss={loss.item():.4f}")


def main():
    model = HairFastDemo(latent_dim=LATENT_DIM, img_size=IMG_SIZE).to(device)

    print("=== Починаємо псевдо-навчання ===")
    simple_train_demo(model, steps=10, lr=1e-4)

    print("=== Інференс після умовного 'навчання' ===")
    with torch.no_grad():

        face_img_test = torch.randn(2, 3, IMG_SIZE, IMG_SIZE, device=device)
        shape_img_test = torch.randn(2, 3, IMG_SIZE, IMG_SIZE, device=device)
        color_img_test = torch.randn(2, 3, IMG_SIZE, IMG_SIZE, device=device)

        out_img, mask64, face_mask, shape_mask = model(
            face_img_test, shape_img_test, color_img_test)

        print("Форма вихідного зображення:", out_img.shape)
        print("Форма маски 64x64 (ShapeAdaptor):", mask64.shape)
        print("Маска волосся для face:", face_mask.shape)
        print("Маска волосся для shape:", shape_mask.shape)

    print("=== Готово! ===")


if __name__ == "__main__":
    main()
