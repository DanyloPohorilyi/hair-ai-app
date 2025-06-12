import cv2
import numpy as np
import tensorflow as tf
from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing.image import img_to_array, array_to_img
from PIL import Image


class ImageSegmenter:
    def __init__(self, hair_model_path, background_model_path, gender_model_path):
        self.hair_model = load_model(hair_model_path)
        self.bg_model = load_model(background_model_path)
        self.gender_model = load_model(gender_model_path)

    def resize_with_padding(self, image, target_size, fill_color=(0, 0, 0)):
        width, height = image.size
        target_width, target_height = target_size
        scale = min(target_width / width, target_height / height)
        new_width, new_height = int(width * scale), int(height * scale)
        resized_image = image.resize(
            (new_width, new_height), Image.Resampling.LANCZOS)
        new_image = Image.new("RGB", target_size, fill_color)
        new_image.paste(resized_image, ((target_width - new_width) //
                        2, (target_height - new_height) // 2))
        return new_image

    def segment_hair(self, image_path, output_mask_path):
        """
        Сегментація волосся з адаптацією результату до розміру оригінального зображення.
        """
        # 1. Завантажуємо оригінальне зображення
        image = Image.open(image_path).convert("RGB")
        orig_size = image.size  # (W, H)

        # 2. Масштабуємо зображення для моделі
        resized_image = image.resize((256, 256), Image.Resampling.LANCZOS)
        image_array = img_to_array(resized_image) / 255.0
        image_array = np.expand_dims(image_array, axis=0)

        # 3. Отримуємо маску з моделі
        predicted_mask = self.hair_model.predict(image_array)[0]
        predicted_mask = (predicted_mask > 0.5).astype(
            np.uint8) * 255  # (256x256)

        # 4. Маску повертаємо до розміру оригінального зображення
        mask_resized = cv2.resize(predicted_mask.squeeze(
        ), orig_size, interpolation=cv2.INTER_NEAREST)

        # 5. Зберігаємо маску
        mask_img = Image.fromarray(mask_resized)
        mask_img.save(output_mask_path)

    def remove_background(self, image_path, output_path):
        """
        Видалення фону із зображення за допомогою моделі.
        """
        image = Image.open(image_path).convert("RGB")
        image_resized = self.resize_with_padding(image, (256, 256))
        image_array = img_to_array(image_resized) / 255.0
        image_array = np.expand_dims(image_array, axis=0)

        # Отримуємо маску фону
        predicted_mask = self.bg_model.predict(image_array)[0]
        predicted_mask = (predicted_mask > 0.3).astype(np.uint8)  # Бінаризація

        image_np = np.array(image_resized)
        removed_bg_image = image_np * np.repeat(predicted_mask, 3, axis=-1)

        # Зберігаємо зображення без фону
        array_to_img(removed_bg_image).save(output_path)

    def extract_hair_only(self, image_path, hair_mask_path, output_path):
        """
        Створює зображення, де залишено лише волосся, використовуючи маску волосся.
        """
        image = Image.open(image_path).convert("RGB")
        hair_mask = Image.open(hair_mask_path).convert(
            "L")  # Завантажуємо маску у відтінках сірого

        # **Змінюємо розмір маски під оригінальне зображення**
        hair_mask_resized = hair_mask.resize(
            image.size, Image.Resampling.NEAREST)

        image_np = np.array(image)
        mask_np = np.array(hair_mask_resized) / 255

        # Застосовуємо маску волосся до зображення
        only_hair = (image_np * np.expand_dims(mask_np, axis=-1)
                     ).astype(np.uint8)
        array_to_img(only_hair).save(output_path)

    def predict_gender(self, image_path):
        """
        Передбачає гендер особи за допомогою нейронної мережі.
        """
        image = Image.open(image_path).convert("RGB")
        image_resized = self.resize_with_padding(image, (128, 128))
        image_array = img_to_array(image_resized) / 255.0
        image_array = np.expand_dims(image_array, axis=0)

        # Отримуємо передбачення
        gender_pred = self.gender_model.predict(image_array)

        # 0 -> Male, 1 -> Female
        return "Male" if np.argmax(gender_pred) == 1 else "Female"
