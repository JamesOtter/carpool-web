# app.py
from fastapi import FastAPI, UploadFile, File
from fastapi.responses import JSONResponse
import shutil
import uuid
import os
import cv2
from ocr_utils import extract_timetable_from_image

app = FastAPI()

UPLOAD_DIR = "./uploads"
os.makedirs(UPLOAD_DIR, exist_ok=True)

def crop_main_region(image_path, output_path):
    image = cv2.imread(image_path)
    if image is None:
        raise ValueError(f"Cannot read image at {image_path}")

    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    _, thresh = cv2.threshold(gray, 200, 255, cv2.THRESH_BINARY_INV)
    contours, _ = cv2.findContours(thresh, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)

    if contours:
        largest = max(contours, key=cv2.contourArea)
        x, y, w, h = cv2.boundingRect(largest)
        cropped = image[y:y+h, x:x+w]
        cv2.imwrite(output_path, cropped)
    else:
        raise ValueError("No contours found in the image")

@app.post("/ocr-timetable")
async def ocr_timetable(file: UploadFile = File(...)):
    ext = file.filename.split(".")[-1]
    uid = str(uuid.uuid4())
    original_path = os.path.join(UPLOAD_DIR, f"{uid}.{ext}")
    cropped_path = os.path.join(UPLOAD_DIR, f"{uid}_cropped.{ext}")

    try:
        # Save uploaded file
        with open(original_path, "wb") as buffer:
            shutil.copyfileobj(file.file, buffer)

        # Crop it before sending to OCR
        crop_main_region(original_path, cropped_path)

        # Call your OCR function on the cropped image
        results = extract_timetable_from_image(cropped_path)

        # Cleanup
        os.remove(original_path)
        os.remove(cropped_path)

        return JSONResponse(content={"success": True, "data": results})

    except Exception as e:
        return JSONResponse(status_code=500, content={"error": str(e)})



