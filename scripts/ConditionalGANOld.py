import os
import json
import random
from gradio_client import Client, handle_file


class HairFastGANApiCaller:
    def __init__(self,
                 hf_space: str = "AIRI-Institute/HairFastGAN",
                 dataset_path: str = r"my-hairstyle-datase\hairstyles",
                 hf_token: str = None):
        # Якщо Space приватний, передавати параметр token=hf_token
        if hf_token:
            self.client = Client(hf_space, token=hf_token)
        else:
            self.client = Client(hf_space)
        self.dataset_path = dataset_path

    def _apply_swap_hair(self,
                         face_img: str,
                         shape_img: str,
                         color_img: str,
                         blending: str = "Article",
                         poisson_iters: float = 0,
                         poisson_erosion: float = 15) -> dict:
        try:
            result = self.client.predict(
                face=handle_file(face_img),
                shape=handle_file(shape_img),
                color=handle_file(color_img),
                blending=blending,
                poisson_iters=poisson_iters,
                poisson_erosion=poisson_erosion,
                api_name="/swap_hair"
            )
            return {
                "result_path": result[0],
                "error_msg": result[1]
            }
        except Exception as e:
            # Повертання повідомлення про помилку, якщо щось піде не так
            return {"result_path": None, "error_msg": str(e)}

    def generate_hairstyles(self, input_json: dict) -> dict:
        results = []
        gender = input_json.get("gender")
        recommended_hairstyles = input_json.get("recommended_hairstyles", [])
        original_img = input_json["output_images"]["no_background"]
        color_img = original_img

        for hairstyle_name in recommended_hairstyles:
            # Формування шляху до директорії з відповідним стилем зачіски
            hairstyle_dir = os.path.join(r"D:\study-1\KURSOVA4\praktik\scripts",
                                         self.dataset_path, gender, hairstyle_name)

            if not os.path.isdir(hairstyle_dir):
                # Якщо немає такої папки, додаємо повідомлення про помилку
                results.append({
                    "hairstyle": hairstyle_name,
                    "error": f"Folder {hairstyle_dir} not found",
                    "result_path": None
                })
                continue

            possible_files = [f for f in os.listdir(hairstyle_dir)
                              if f.lower().endswith((".png", ".jpg", ".jpeg"))]
            if not possible_files:
                results.append({
                    "hairstyle": hairstyle_name,
                    "error": f"No hairstyle images in {hairstyle_dir}",
                    "result_path": None
                })
                continue

            shape_file = random.choice(possible_files)
            shape_img_path = os.path.join(hairstyle_dir, shape_file)

            swap_result = self._apply_swap_hair(
                face_img=original_img,
                shape_img=shape_img_path,
                color_img=color_img,
                blending="Article",
                poisson_iters=0,
                poisson_erosion=15
            )

            results.append({
                "hairstyle": hairstyle_name,
                "shape_file": shape_file,
                "result_path": swap_result["result_path"],
                "error_msg": swap_result["error_msg"]
            })

        return {
            "status": "ok",
            "generated": results
        }
