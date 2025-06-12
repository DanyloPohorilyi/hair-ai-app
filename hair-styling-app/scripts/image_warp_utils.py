import cv2
import numpy as np
from scipy.spatial import Delaunay
import torch
import mediapipe as mp
import cv2
import numpy as np
from PIL import Image


def enhanced_generate_hairless_image(image_path, hair_mask_path, output_path=None):
    """
    Покращене видалення волосся з фоном, який зливається.
    Використовується комбінований підхід: inpainting + background filling + Gaussian blur.
    """
    image = cv2.imread(image_path)
    mask = cv2.imread(hair_mask_path, cv2.IMREAD_GRAYSCALE)

    if image is None or mask is None:
        raise FileNotFoundError("Зображення або маска не знайдені.")

    # Ресайз маски, якщо потрібно
    if image.shape[:2] != mask.shape[:2]:
        mask = cv2.resize(
            mask, (image.shape[1], image.shape[0]), interpolation=cv2.INTER_NEAREST)

    # Створення маски для inpainting
    inpaint_mask = (mask > 128).astype(np.uint8) * 255

    # Inpainting результат
    inpainted = cv2.inpaint(image, inpaint_mask,
                            inpaintRadius=3, flags=cv2.INPAINT_TELEA)

    # Отримання маски межі
    kernel = np.ones((3, 3), np.uint8)
    edge = cv2.morphologyEx(inpaint_mask, cv2.MORPH_GRADIENT, kernel)

    # Gaussian blur по всій масці
    blurred = cv2.GaussianBlur(inpainted, (11, 11), 0)

    # Перехідна альфа-маска для плавного злиття країв
    distance = cv2.distanceTransform(255 - edge, cv2.DIST_L2, 5)
    distance = np.clip(distance / 10.0, 0, 1)
    alpha = distance[..., np.newaxis]

    blended = inpainted * alpha + blurred * (1 - alpha)
    blended = blended.astype(np.uint8)

    if output_path:
        cv2.imwrite(output_path, blended)

    # Перетворення для відображення
    return cv2.cvtColor(blended, cv2.COLOR_BGR2RGB)


# --- Ініціалізація FaceMesh ---
mp_face_mesh = mp.solutions.face_mesh
face_mesh = mp_face_mesh.FaceMesh(static_image_mode=True, max_num_faces=1)

# --- Ключові точки ---
KEYPOINT_INDICES = list(set([
    0, 1, 2, 3, 4, 5, 6, 7, 8,        # Лінія чола по центру
    10, 13, 14, 15, 16, 17, 18, 20,   # Верхня частина голови, центр
    22, 23, 24, 26, 28, 30,           # Верхні лобові краї та центр лоба
    33, 36, 54, 67, 70, 76, 82, 84,   # Ліва брова, вилиця, нижче ока
    87, 93, 101, 107, 109, 129,       # Середина обличчя і скронева зона
    132, 144, 151, 152, 153, 178,     # Щоки, лінія щелепи
    205, 209, 234, 263, 266, 271,     # Правий контур
    284, 296, 297, 300, 306, 314,     # Праве око, скроні
    323, 336, 338, 358, 361, 378,     # Права брова та бокові
    398, 425, 429, 454                # Вуха та низ боків
]))


# --- Перетворення тензора в зображення ---
def tensor_to_image(tensor):
    tensor = tensor.cpu().clone().detach()
    tensor = (tensor * 0.5 + 0.5).clamp(0, 1)
    return (tensor.permute(1, 2, 0).numpy() * 255).astype(np.uint8)

# --- Отримання координат ---


def get_landmark_coords_from_tensor(tensor_img, idx_list):
    img = tensor_img.clone()
    img = img * 0.5 + 0.5
    img = (img.permute(1, 2, 0).cpu().numpy() * 255).astype(np.uint8)
    img_bgr = cv2.cvtColor(img, cv2.COLOR_RGB2BGR)
    results = face_mesh.process(cv2.cvtColor(img_bgr, cv2.COLOR_BGR2RGB))
    coords = []
    if results.multi_face_landmarks:
        for idx in idx_list:
            lm = results.multi_face_landmarks[0].landmark[idx]
            x, y = int(lm.x * 256), int(lm.y * 256)
            coords.append([x, y])
    return np.array(coords, dtype=np.float32) if len(coords) == len(idx_list) else None


def add_border_points(points, img_size=256):
    if isinstance(img_size, int):
        w = h = img_size
    else:
        w, h = img_size

    border = [
        [0, 0], [w // 2, 0], [w - 1, 0],
        [0, h // 2], [w - 1, h // 2],
        [0, h - 1], [w // 2, h - 1], [w - 1, h - 1]
    ]
    return np.concatenate([points, np.array(border, dtype=np.float32)], axis=0)


# --- Warp зображення шматками ---


def warp_image_piecewise_affine(src_img, src_points, dst_points, size=(256, 256)):
    is_gray = len(src_img.shape) == 2
    h, w = size
    output = np.zeros((h, w) if is_gray else (h, w, 3), dtype=np.uint8)

    tri = Delaunay(src_points)

    for tri_indices in tri.simplices:
        src_tri = np.float32([src_points[i] for i in tri_indices])
        dst_tri = np.float32([dst_points[i] for i in tri_indices])

        r1 = cv2.boundingRect(src_tri)
        r2 = cv2.boundingRect(dst_tri)

        src_offset = np.array([[p[0] - r1[0], p[1] - r1[1]]
                              for p in src_tri], dtype=np.float32)
        dst_offset = np.array([[p[0] - r2[0], p[1] - r2[1]]
                              for p in dst_tri], dtype=np.float32)

        src_crop = src_img[r1[1]:r1[1]+r1[3], r1[0]:r1[0]+r1[2]]
        if src_crop.shape[0] == 0 or src_crop.shape[1] == 0:
            continue

        M = cv2.getAffineTransform(src_offset, dst_offset)
        warped = cv2.warpAffine(
            src_crop, M, (r2[2], r2[3]), flags=cv2.INTER_LINEAR, borderMode=cv2.BORDER_REFLECT_101)

        triangle_mask = np.zeros((r2[3], r2[2]), dtype=np.uint8)
        cv2.fillConvexPoly(triangle_mask, np.int32(dst_offset), 1, 16, 0)

        y1, y2 = r2[1], r2[1] + r2[3]
        x1, x2 = r2[0], r2[0] + r2[2]

        if y2 > h or x2 > w:
            continue

        if is_gray:
            roi = output[y1:y2, x1:x2]
            triangle_mask_resized = triangle_mask[:roi.shape[0], :roi.shape[1]]
            warped = warped[:roi.shape[0], :roi.shape[1]]
            output[y1:y2, x1:x2] = roi * \
                (1 - triangle_mask_resized) + warped * triangle_mask_resized
        else:
            roi = output[y1:y2, x1:x2]
            triangle_mask_3ch = np.stack([triangle_mask]*3, axis=-1)
            if roi.shape != warped.shape:
                warped = cv2.resize(warped, (roi.shape[1], roi.shape[0]))
                triangle_mask_3ch = cv2.resize(
                    triangle_mask_3ch, (roi.shape[1], roi.shape[0]))
            output[y1:y2, x1:x2] = roi * \
                (1 - triangle_mask_3ch) + warped * triangle_mask_3ch

    return output


def create_feathered_mask(binary_mask, feather_radius=15):
    """
    Створює альфа-маску з поступовим зникненням на краях маски.
    """
    blur = cv2.GaussianBlur(
        binary_mask, (feather_radius*2+1, feather_radius*2+1), 0)
    mask = (binary_mask / 255.0).astype(np.float32)
    feathered = np.clip(blur / 255.0, 0, 1) * mask
    return np.stack([feathered]*3, axis=-1)


def align_and_transfer_mask_with_hair(dataset, idx_src, idx_tgt):
    hs, _, _, orig_s, _ = dataset[idx_src]
    _, hair_tgt, mask_t, orig_t, _ = dataset[idx_tgt]

    hs_img = tensor_to_image(hs)
    hair_tgt_img = tensor_to_image(hair_tgt)
    orig_s_img = tensor_to_image(orig_s)
    orig_t_img = tensor_to_image(orig_t)

    mask_t_img = (mask_t.squeeze().cpu().numpy() * 255).astype(np.uint8)

    src_pts = get_landmark_coords_from_tensor(orig_s, KEYPOINT_INDICES)
    tgt_pts = get_landmark_coords_from_tensor(orig_t, KEYPOINT_INDICES)

    if src_pts is None or tgt_pts is None:
        raise ValueError("Не вдалося знайти всі лендмарки")

    src_pts = add_border_points(src_pts, (hs_img.shape[1], hs_img.shape[0]))
    tgt_pts = add_border_points(tgt_pts, (hs_img.shape[1], hs_img.shape[0]))

    # Вирівнюємо маску і волосся
    warped_mask = warp_image_piecewise_affine(
        mask_t_img, tgt_pts, src_pts, size=(256, 256))
    warped_hair = warp_image_piecewise_affine(
        hair_tgt_img, tgt_pts, src_pts, size=(256, 256))
    background_mask = (warped_mask < 30) | ((warped_hair > 240).all(axis=-1))
    warped_hair[background_mask] = hs_img[background_mask]
    # Створюємо feathered alpha маску
    # Feathered маска — розмиття країв
    feathered_mask = cv2.GaussianBlur(
        warped_mask, (21, 21), 0).astype(np.float32) / 255.0
    feathered_mask = np.clip(feathered_mask, 0.0, 1.0)
    feathered_mask = np.stack([feathered_mask]*3, axis=-1)
    feathered_mask = create_feathered_mask(warped_mask, feather_radius=5)

    # --- ВИПРАВЛЕНЕ ЗЛИТТЯ ---
    # Використовуємо hairless (hs_img) як фон, а не чорний
    result = (hs_img.astype(np.float32) * (1 - feathered_mask) +
              warped_hair.astype(np.float32) * feathered_mask).clip(0, 255).astype(np.uint8)

    return [
        orig_s_img,             # Зображення користувача
        orig_t_img,             # Бажане стилеве зображення
        warped_mask,            # Маска, вирівняна під користувача
        result                  # Злите фінальне зображення
    ]


def resize_with_padding(img, target_size=(256, 256), fill_color=(0, 0, 0)):
    original_size = img.size
    ratio = min(target_size[0] / original_size[0],
                target_size[1] / original_size[1])
    new_size = (int(original_size[0] * ratio), int(original_size[1] * ratio))
    resized_img = img.resize(new_size, Image.Resampling.LANCZOS)

    new_img = Image.new("RGB", target_size, fill_color)
    paste_position = ((target_size[0] - new_size[0]) //
                      2, (target_size[1] - new_size[1]) // 2)
    new_img.paste(resized_img, paste_position)
    return new_img


def remove_background_from_style_hair(hair_path, mask_path):
    hair = Image.open(hair_path).convert("RGB")
    mask = Image.open(mask_path).convert("L").resize(
        hair.size, Image.Resampling.NEAREST)
    mask_np = np.array(mask) > 128
    hair_np = np.array(hair)
    hair_np[~mask_np] = [255, 255, 255]
    return Image.fromarray(hair_np)


def transfer_hairstyle(image_path, style_image_path, hair_mask_path, style_mask_path, style_hair_path, save_path=None):
    """
    Повний процес: видалення волосся -> вирівнювання зачіски -> злиття
    :param image_path: шлях до зображення користувача
    :param style_image_path: шлях до стилізованої зачіски
    :param hair_mask_path: маска волосся користувача
    :param style_mask_path: маска волосся стилю
    :param style_hair_path: обрізане зображення волосся стилю
    :param save_path: шлях для збереження фінального результату
    :return: фінальне зображення з новою зачіскою
    """
    from torchvision import transforms
    from PIL import Image

    # === Видалення волосся (inpainting) ===
    hairless_rgb = enhanced_generate_hairless_image(image_path, hair_mask_path)

    # --- Функція для перетворення зображення у тензор ---
    def to_tensor(img):
        transform = transforms.Compose([
            transforms.ToTensor(),
            transforms.Normalize([0.5] * 3, [0.5] * 3)
        ])
        if isinstance(img, np.ndarray):
            img = Image.fromarray(img)
        return transform(img).clone()

    def load_tensor(path):
        img = Image.open(path).convert("RGB")
        img = resize_with_padding(img, (256, 256))
        return to_tensor(np.array(img))

    def load_mask_tensor(path):
        mask = Image.open(path).convert("L").resize((256, 256))
        mask = np.array(mask).astype(np.float32) / 255.0
        return torch.from_numpy(mask).unsqueeze(0)

    # === Формуємо тензори ===
    hairless_tensor = to_tensor(hairless_rgb)
    orig_tensor = load_tensor(image_path)
    style_img_tensor = load_tensor(style_image_path)
    style_mask_tensor = load_mask_tensor(style_mask_path)
    style_hair_tensor = load_tensor(style_hair_path)

    # === Формуємо датасет ===
    dataset = [
        (hairless_tensor, None, None, orig_tensor, None),
        (None, style_hair_tensor, style_mask_tensor, style_img_tensor, None)
    ]

    # === Застосовуємо трансфер ===
    result_images = align_and_transfer_mask_with_hair(dataset, 0, 1)
    final_image = result_images[-1]

    # === Зберігаємо або повертаємо ===
    if save_path:
        final_bgr = cv2.cvtColor(final_image, cv2.COLOR_RGB2BGR)
        cv2.imwrite(save_path, final_bgr)

    global face_mesh
    if face_mesh is not None:
        face_mesh.close()
        face_mesh = None

    return final_image
