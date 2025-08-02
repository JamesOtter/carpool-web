import cv2
import os

def crop_main_region(image_path, output_path='cropped_output.jpg'):
    image = cv2.imread(image_path)
    if image is None:
        print(f"[ERROR] Cannot read image at {image_path}")
        return

    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)

    # Binary threshold to highlight content
    _, thresh = cv2.threshold(gray, 200, 255, cv2.THRESH_BINARY_INV)

    # Find external contours
    contours, _ = cv2.findContours(thresh, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)

    if contours:
        # Largest contour (assumed to be main content)
        largest = max(contours, key=cv2.contourArea)
        x, y, w, h = cv2.boundingRect(largest)

        # Crop and save
        cropped = image[y:y+h, x:x+w]
        cv2.imwrite(output_path, cropped)
        print(f"[SUCCESS] Cropped image saved to {output_path}")
    else:
        print("[WARNING] No contours found; image was not cropped.")


input_path = "test_images/test3.png"  # Replace with your image path
output_path = "cropped_timetable.jpg"
crop_main_region(input_path, output_path)
