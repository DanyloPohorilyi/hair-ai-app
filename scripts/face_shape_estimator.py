import cv2
import mediapipe as mp
import numpy as np


class FaceShapeAnalyzer:
    def __init__(self, image):
        self.image = image
        self.mp_face_mesh = mp.solutions.face_mesh
        self.face_mesh = self.mp_face_mesh.FaceMesh(
            static_image_mode=True, max_num_faces=1, refine_landmarks=True)
        self.landmarks = self.process_image()

    def process_image(self):
        image_rgb = cv2.cvtColor(self.image, cv2.COLOR_BGR2RGB)
        results = self.face_mesh.process(image_rgb)
        if results.multi_face_landmarks:
            return results.multi_face_landmarks[0].landmark
        return None

    def _get_landmark_coords(self, index):
        if self.landmarks and 0 <= index < len(self.landmarks):
            return np.array([self.landmarks[index].x, self.landmarks[index].y])
        return None

    def get_face_measurements(self):
        if not self.landmarks:
            return None

        forehead_width = np.linalg.norm(
            self._get_landmark_coords(104) - self._get_landmark_coords(284))
        cheekbone_width = np.linalg.norm(
            self._get_landmark_coords(234) - self._get_landmark_coords(454))
        jaw_width = np.linalg.norm(self._get_landmark_coords(
            135) - self._get_landmark_coords(367))
        face_height = np.linalg.norm(self._get_landmark_coords(
            10) - self._get_landmark_coords(152))

        return {
            "forehead_width": forehead_width,
            "cheekbone_width": cheekbone_width,
            "jaw_width": jaw_width,
            "face_height": face_height
        }

    def calculate_chin_angle(self):
        if not self.landmarks:
            return None

        chin_tip = self._get_landmark_coords(152)
        left_jaw_corner = self._get_landmark_coords(135)
        right_jaw_corner = self._get_landmark_coords(367)

        if chin_tip is None or left_jaw_corner is None or right_jaw_corner is None:
            return None

        vector_left = left_jaw_corner - chin_tip
        vector_right = right_jaw_corner - chin_tip

        dot_product = np.dot(vector_left, vector_right)
        norm_left = np.linalg.norm(vector_left)
        norm_right = np.linalg.norm(vector_right)

        if norm_left == 0 or norm_right == 0:
            return None

        cos_angle = dot_product / (norm_left * norm_right)
        cos_angle = np.clip(cos_angle, -1.0, 1.0)
        angle = np.degrees(np.arccos(cos_angle))
        return angle

    def get_chin_shape(self):
        angle = self.calculate_chin_angle()
        if angle is None:
            return "Unknown"

        if angle < 140:
            return "Sharp"
        else:
            return "Round"

    def determine_face_shape(self):
        measurements = self.get_face_measurements()
        if not measurements:
            return "Face not detected"

        forehead_ratio = measurements["forehead_width"] / \
            measurements["face_height"]
        cheekbone_ratio = measurements["cheekbone_width"] / \
            measurements["face_height"]
        jaw_ratio = measurements["jaw_width"] / measurements["face_height"]
        height_to_cheekbone_ratio = measurements["face_height"] / \
            measurements["cheekbone_width"]

        chin_type = self.get_chin_shape()

        print("Face Measurements (normalized by height):")
        print(f"  Forehead width (ratio): {forehead_ratio:.2f}")
        print(f"  Cheekbone width (ratio): {cheekbone_ratio:.2f}")
        print(f"  Jaw width (ratio): {jaw_ratio:.2f}")
        print(f"  Height to Cheekbone Ratio: {height_to_cheekbone_ratio:.2f}")
        print(f"  Chin type: {chin_type}")

        # Shape determination logic
        if height_to_cheekbone_ratio > 1.45 and \
           abs(forehead_ratio - cheekbone_ratio) < 0.05 and \
           abs(cheekbone_ratio - jaw_ratio) < 0.05:
            return "Oblong"

        if height_to_cheekbone_ratio >= 1.3 and height_to_cheekbone_ratio <= 1.45 and \
           cheekbone_ratio > forehead_ratio and forehead_ratio > jaw_ratio and \
           chin_type == "Round":
            return "Oval"

        if 0.95 <= height_to_cheekbone_ratio <= 1.25 and \
           abs(forehead_ratio - cheekbone_ratio) < 0.05 and \
           abs(cheekbone_ratio - jaw_ratio) < 0.05 and \
           chin_type == "Round":
            return "Round"

        if 0.95 <= height_to_cheekbone_ratio <= 1.25 and \
           abs(forehead_ratio - cheekbone_ratio) < 0.05 and \
           abs(cheekbone_ratio - jaw_ratio) < 0.05 and \
           chin_type == "Sharp":
            return "Square"

        if forehead_ratio > cheekbone_ratio and cheekbone_ratio > jaw_ratio and chin_type == "Sharp":
            return "Heart"

        if cheekbone_ratio > forehead_ratio and cheekbone_ratio > jaw_ratio and chin_type == "Sharp":
            return "Diamond"

        if jaw_ratio > cheekbone_ratio and cheekbone_ratio > forehead_ratio:
            return "Triangle"

        if height_to_cheekbone_ratio > 1.35 and \
           abs(forehead_ratio - cheekbone_ratio) < 0.05 and \
           abs(cheekbone_ratio - jaw_ratio) < 0.05 and \
           chin_type == "Sharp":
            return "Rectangle"

        return "Unknown"

    def save_face_mask_with_labels(self, output_path):
        if not self.landmarks:
            print("Face not detected, mask not created.")
            return

        used_indices = [10, 152, 234, 454, 135, 367, 104, 284, 140, 379]

        image_with_labels = self.image.copy()
        h, w, _ = self.image.shape

        for i in used_indices:
            point_coords = self._get_landmark_coords(i)
            if point_coords is not None:
                x, y = int(point_coords[0] * w), int(point_coords[1] * h)
                cv2.circle(image_with_labels, (x, y), 3, (0, 0, 255), -1)
                cv2.putText(image_with_labels, str(i), (x + 5, y - 5),
                            cv2.FONT_HERSHEY_SIMPLEX, 0.5, (0, 255, 0), 1)

        cv2.imwrite(output_path, image_with_labels)
        print(f"Face mask with labels saved as {output_path}")
