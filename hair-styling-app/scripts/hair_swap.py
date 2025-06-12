import os
import sys
import json
import replicate
import traceback
import cv2
import requests
from io import BytesIO
from PIL import Image

# Припускаємо, що ці модулі існують і працюють правильно
from segmentation import ImageSegmenter
from face_shape_estimator import FaceShapeAnalyzer
from hairstyle_recommendation import HairstyleRecommender

LOG_FILE = "D:/study-1/KURSOVA4/praktik/hair-styling-app/storage/logs/python_log.txt"
# GENERATED_DIR більше не потрібен для збереження локально, але може бути корисним для інших цілей.
# GENERATED_DIR = "D:/study-1/KURSOVA4/praktik/hair-styling-app/public/storage/app/public/generated"


def log_message(message):
    with open(LOG_FILE, "a", encoding="utf-8") as log_file:
        log_file.write("[LOG] " + message + "\n")


try:
    log_message("===== Start Hair Styling Script =====")

    # Перевірка аргументу
    if len(sys.argv) < 2:
        error_msg = json.dumps({"error": "No image path provided"})
        log_message(error_msg)
        print(error_msg)
        sys.exit(1)

    image_path = sys.argv[1]
    log_message(f"Step 1: Image path received: {image_path}")

    if not os.path.exists(image_path):
        error_msg = json.dumps({"error": "Image not found"})
        log_message(error_msg)
        print(error_msg)
        sys.exit(1)

    # Завантаження моделей
    log_message("Step 2: Loading models...")
    hair_model_path = "models/hair_segmentation_model.keras"
    background_model_path = "models/best_model.keras"
    gender_model_path = "models/celeba_gender_model.keras"

    segmenter = ImageSegmenter(
        hair_model_path, background_model_path, gender_model_path)
    log_message("Step 2.1: ImageSegmenter initialized.")

    recommender = HairstyleRecommender()
    log_message("Step 2.2: HairstyleRecommender initialized.")

    # Аналіз обличчя
    log_message("Step 3: Analyzing face shape and gender...")
    image_cv2 = cv2.imread(image_path)
    face_analyzer = FaceShapeAnalyzer(image_cv2)
    face_shape = face_analyzer.determine_face_shape()
    log_message(f"Face shape determined: {face_shape}")

    gender = segmenter.predict_gender(image_path)
    log_message(f"Gender predicted: {gender}")

    # Рекомендації
    log_message("Step 4: Getting hairstyle recommendations...")
    recommendations = recommender.get_recommendations(face_shape, gender)
    log_message(f"Recommendations found: {recommendations}")

    if not recommendations:
        log_message("No recommendations returned. Exiting.")
        print(json.dumps({"error": "No recommendations available"}))
        sys.exit(1)

    # Виклик Replicate
    log_message("Step 5: Calling Replicate API...")
    os.environ["REPLICATE_API_TOKEN"] = "r8_MrwQZq8Wa2onw5WltyrVuYkchGKYiHp1NTqQj"
    # os.makedirs(GENERATED_DIR, exist_ok=True) # Цей рядок більше не потрібен, якщо не зберігаємо локально

    final_results = []

    for idx, hairstyle in enumerate(recommendations[:4]):
        color = "natural"
        log_message(f"Generating hairstyle {idx + 1}: {hairstyle}")

        with open(image_path, "rb") as image_file:
            try:
                output = replicate.run("wty-ustc/hairclip:b95cb2a16763bea87ed7ed851d5a3ab2f4655e94bcfb871edba029d4814fa587",
                                       input={
                                           "image": image_file,
                                           "editing_type": "hairstyle",
                                           "color_description": color,
                                           "hairstyle_description": hairstyle
                                       }
                                       )

                # Якщо output — це список або FileOutput, отримаємо URL як рядок
                if isinstance(output, list):
                    output_url = str(output[0])  # Перший файл у списку
                else:
                    output_url = str(output)  # Примусове приведення до рядка

                log_message(f"Image generated at: {output_url}")

                final_results.append({
                    "hairstyle": hairstyle,
                    "generated_image_url": output_url
                })

            except Exception as api_error:
                log_message(
                    f"Error during replicate.run for hairstyle {hairstyle}: {str(api_error)}")
                continue  # Продовжуємо до наступної зачіски, якщо ця не вдалася

    # Вивід результатів
    response = {
        "face_shape": face_shape,
        "gender": gender,
        "recommended_hairstyles": final_results
    }

    log_message("===== Script Finished Successfully =====")
    print(json.dumps(response, ensure_ascii=False))

except Exception as e:
    error_message = traceback.format_exc()
    log_message(f"Error: {error_message}")
    print(json.dumps({"error": "Processing failed"}))
