import cv2
import numpy as np
from sklearn.cluster import KMeans
from PIL import Image


class HairColorAnalyzer:
    def __init__(self, image_path, mask_path, k=3, fill_color=(0, 0, 0)):
        # Завантажуємо зображення та маску
        self.image = cv2.imread(image_path)
        self.mask = cv2.imread(mask_path, cv2.IMREAD_GRAYSCALE)

        # Перевіряємо, чи розміри збігаються
        if self.image.shape[:2] != self.mask.shape[:2]:
            self.mask = cv2.resize(
                self.mask, (self.image.shape[1], self.image.shape[0]), interpolation=cv2.INTER_NEAREST)

        self.k = k
        self.fill_color = fill_color

    def get_dominant_color(self):
        """
        Визначає домінантний колір волосся за допомогою кластеризації K-Means.
        """
        hair_pixels = self.image[self.mask > 128]

        # Переконуємося, що є хоча б кілька пікселів для аналізу
        if len(hair_pixels) == 0:
            print("❌ Помилка: У масці волосся немає пікселів для аналізу.")
            # Повертаємо чорний колір як значення за замовчуванням
            return (0, 0, 0)

        # Виконуємо кластеризацію K-Means
        kmeans = KMeans(n_clusters=self.k, random_state=42, n_init=10)
        kmeans.fit(hair_pixels)
        dominant_color = kmeans.cluster_centers_[
            np.argmax(np.bincount(kmeans.labels_))]

        return dominant_color.astype(int)  # Повертаємо у форматі (R, G, B)

    def save_hair_only_image(self, output_path):
        """
        Зберігає зображення, на якому видно тільки волосся.
        """
        hair_only = np.full_like(
            self.image, self.fill_color)  # Заповнюємо фон заданим кольором
        hair_only[self.mask > 128] = self.image[self.mask > 128]
        Image.fromarray(cv2.cvtColor(
            hair_only, cv2.COLOR_BGR2RGB)).save(output_path)
