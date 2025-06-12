import os
import json


class HairstyleRecommender:
    def __init__(self, json_path="D:/study-1/KURSOVA4/praktik/scripts/best_haircut.json"):
        with open(json_path, "r", encoding="utf-8") as file:
            self.data = json.load(file)

    def get_recommendations(self, face_shape, gender):
        # Лог: отримання рекомендацій
        print(
            f"[RECOMMENDER] Looking for styles for face_shape='{face_shape}' and gender='{gender}'")

        matches = [
            entry["hairstyle"]
            for entry in self.data
            if entry["face_shape"].lower() == face_shape.lower()
            and entry["gender"].lower() == gender.lower()
        ]

        # Видаляємо дублікати, якщо вони є
        unique_hairstyles = list(set(matches))
        print(
            f"[RECOMMENDER] Found {len(unique_hairstyles)} matching styles: {unique_hairstyles}")

        return unique_hairstyles
