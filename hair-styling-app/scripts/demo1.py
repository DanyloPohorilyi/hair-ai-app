import replicate
import os
import requests
from PIL import Image
from io import BytesIO
import matplotlib.pyplot as plt

# === Встановлюємо API токен ===
os.environ["REPLICATE_API_TOKEN"] = "r8_MrwQZq8Wa2onw5WltyrVuYkchGKYiHp1NTqQj"

# === Відкриваємо локальне зображення ===
image_path = "D:/study-1/KURSOVA4/project/example/input/example1.jpg"
with open(image_path, "rb") as image_file:
    # === Запускаємо HairCLIP ===
    output = replicate.run(
        "wty-ustc/hairclip:b95cb2a16763bea87ed7ed851d5a3ab2f4655e94bcfb871edba029d4814fa587",
        input={
            "image": image_file,
            "editing_type": "hairstyle",
            "color_description": "",
            "hairstyle_description": "undercut hairstyle"
        }
    )

# === Вивід результату ===
print("Result URL:", output)

# === Завантаження зображення ===
response = requests.get(output)
result_image = Image.open(BytesIO(response.content))

# === Відображення зображення ===
plt.imshow(result_image)
plt.axis("off")
plt.title("HairCLIP Result")
plt.show()
