import os
import cv2
import numpy as np
import torch
import shutil
from PIL import Image
from torchvision import transforms
import matplotlib.pyplot as plt

from segmentation import ImageSegmenter
from face_shape_estimator import FaceShapeAnalyzer
from hair_color_def import HairColorAnalyzer
from hairstyle_recommendation import HairstyleRecommender
from image_warp_utils import align_and_transfer_mask_with_hair, remove_background_from_style_hair
from image_warp_utils import enhanced_generate_hairless_image  # додано inpainting

# === Допоміжні функції ===


def show_image(title, image):
    if isinstance(image, str):
        image = cv2.imread(image)
        image = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
    elif image.shape[2] == 3 and image.dtype == np.uint8:
        pass  # уже в RGB
    plt.imshow(image)
    plt.title(title)
    plt.axis("off")
    plt.show()


def resize_with_padding(img, target_size=(256, 256)):
    original_size = img.size
    ratio = min(target_size[0] / original_size[0],
                target_size[1] / original_size[1])
    new_size = (int(original_size[0] * ratio), int(original_size[1] * ratio))
    resized_img = img.resize(new_size, Image.Resampling.LANCZOS)

    new_img = Image.new("RGB", target_size)
    paste_position = ((target_size[0] - new_size[0]) //
                      2, (target_size[1] - new_size[1]) // 2)
    new_img.paste(resized_img, paste_position)
    return new_img


def load_tensor_image(path):
    img = Image.open(path).convert("RGB")
    img = resize_with_padding(img, (256, 256))
    transform = transforms.Compose([
        transforms.ToTensor(),
        transforms.Normalize([0.5] * 3, [0.5] * 3)
    ])
    return transform(img)


def load_mask_tensor(path, size=(256, 256)):
    mask = Image.open(path).convert("L").resize(size, Image.Resampling.NEAREST)
    mask = np.array(mask).astype(np.float32) / 255.0
    return torch.from_numpy(mask).unsqueeze(0)


# === Шляхи ===
image_path = "D:/study-1/KURSOVA4/project/example/input/example1.jpg"
hair_model_path = "D:/study-1/KURSOVA4/praktik/scripts/models/hair_segmentation_model.keras"
background_model_path = "D:/study-1/KURSOVA4/praktik/scripts/models/best_model.keras"
gender_model_path = "D:/study-1/KURSOVA4/praktik/scripts/models/celeba_gender_model.keras"

# === Ініціалізація моделей ===
segmenter = ImageSegmenter(
    hair_model_path, background_model_path, gender_model_path)
recommender = HairstyleRecommender()

# === Папки ===
base_name = os.path.splitext(os.path.basename(image_path))[0]
output_dir = os.path.join(os.path.dirname(image_path), "output")
os.makedirs(output_dir, exist_ok=True)

mask_path = os.path.join(output_dir, f"hair_mask_{base_name}.png")
hair_only_path = os.path.join(output_dir, f"only_hair_{base_name}.png")
hairless_path = os.path.join(output_dir, f"hairless_{base_name}.png")

# === Сегментація волосся та видалення його з обличчя ===
segmenter.segment_hair(image_path, mask_path)
segmenter.extract_hair_only(image_path, mask_path, hair_only_path)

hairless_rgb = enhanced_generate_hairless_image(
    image_path, mask_path, output_path=hairless_path)
hairless_tensor = load_tensor_image(hairless_path)
orig_tensor = load_tensor_image(image_path)

# === Аналіз форми обличчя і статі ===
face_analyzer = FaceShapeAnalyzer(cv2.imread(image_path))
face_shape = face_analyzer.determine_face_shape()
gender = segmenter.predict_gender(image_path)

# === Аналіз кольору волосся ===
hair_analyzer = HairColorAnalyzer(hair_only_path, mask_path)
dominant_color = hair_analyzer.get_dominant_color()
hex_color = '#' + ''.join(format(int(c), '02X') for c in dominant_color)

# === Рекомендації ===
recommendations = recommender.get_recommendations(face_shape, gender)

# === Обробка кожної стилізованої зачіски ===
for idx, rec in enumerate(recommendations[:4]):
    style_name = rec["hairstyle"]
    style_img_path = rec["image_path"]
    style_base = os.path.splitext(os.path.basename(style_img_path))[0]

    style_dir = os.path.join(
        output_dir, f"style_{idx+1}_{style_name.replace(' ', '_')}")
    os.makedirs(style_dir, exist_ok=True)

    style_copy_path = os.path.join(style_dir, f"original_{style_base}.jpg")
    style_mask_path = os.path.join(style_dir, f"mask_{style_base}.png")
    style_hair_path = os.path.join(style_dir, f"hair_{style_base}.png")

    shutil.copy(style_img_path, style_copy_path)
    segmenter.segment_hair(style_img_path, style_mask_path)
    segmenter.extract_hair_only(
        style_img_path, style_mask_path, style_hair_path)

    style_img_tensor = load_tensor_image(style_copy_path)
    style_mask_tensor = load_mask_tensor(style_mask_path)
    cleaned_style_hair = remove_background_from_style_hair(
        style_hair_path, style_mask_path)
    cleaned_style_hair = resize_with_padding(cleaned_style_hair, (256, 256))
    style_hair_tensor = transforms.Compose([
        transforms.ToTensor(),
        transforms.Normalize([0.5]*3, [0.5]*3)
    ])(cleaned_style_hair)

    # === Формування input dataset ===
    dataset = [
        (hairless_tensor, None, None, orig_tensor, None),
        (None, style_hair_tensor, style_mask_tensor, style_img_tensor, None)
    ]

    # === Застосування трансферу ===
    result_images = align_and_transfer_mask_with_hair(dataset, 0, 1)
    final_result = result_images[-1]
    transfer_path = os.path.join(output_dir, f"transferred_{idx+1}.jpg")

    final_result_bgr = cv2.cvtColor(final_result, cv2.COLOR_RGB2BGR)
    cv2.imwrite(transfer_path, final_result_bgr)

    # === Вивід результату ===
    show_image(
        f"Final Result {idx+1}: {style_name} ({style_base}.jpg)", final_result)
