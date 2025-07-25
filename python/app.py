# app.py
from fastapi import FastAPI, UploadFile, File
from fastapi.responses import JSONResponse
import shutil
import uuid
import os
from ocr_utils import extract_timetable_from_image

app = FastAPI()

UPLOAD_DIR = "./uploads"
os.makedirs(UPLOAD_DIR, exist_ok=True)

@app.post("/ocr-timetable")
async def ocr_timetable(file: UploadFile = File(...)):
    # Save uploaded file
    ext = file.filename.split(".")[-1]
    temp_filename = f"{uuid.uuid4()}.{ext}"
    temp_filepath = os.path.join(UPLOAD_DIR, temp_filename)

    with open(temp_filepath, "wb") as buffer:
        shutil.copyfileobj(file.file, buffer)

    try:
        # Call your OCR function
        results = extract_timetable_from_image(temp_filepath)
    except Exception as e:
        return JSONResponse(status_code=500, content={"error": str(e)})

    # Optionally delete the file after processing
    os.remove(temp_filepath)

    return JSONResponse(content={"success": True, "data": results})

