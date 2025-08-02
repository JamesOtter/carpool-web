from paddleocr import PaddleOCR
import re
import json
import os
import time
import cv2
import sys

os.environ['GLOG_minloglevel'] = '2'  # Suppresses INFO and WARNING logs
os.environ['FLAGS_log_level'] = '3'   # Suppresses logs from Paddle internals

def is_time(text):
    return re.match(r'^\d{1,2}:\d{2}$', text)

def is_day(text):
    return text.strip().lower()[:3] in ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun']

def is_classroom_code(text):
    return re.match(r'^[BDEHILNP]', text.strip())

def box_center(box):
    # box is [[x1, y1], [x2, y2], [x3, y3], [x4, y4]]
    xs = [pt[0] for pt in box]
    ys = [pt[1] for pt in box]
    return sum(xs)/4, sum(ys)/4

# Start timer
# overall_start = time.time()

 # Get the image path from Laravel
# if len(sys.argv) < 2:
#     print("No image path provided")
#     sys.exit(1)
#
# img_path = sys.argv[1]

# For Testing image
# Get base directory of the script
# BASE_DIR = os.path.dirname(os.path.abspath(__file__))
# Construct full path to the image
# img_path = os.path.join(BASE_DIR, 'test_images', 'sample1.jpeg')
# img_path = 'test_images/sample1.jpeg'

# start = time.time()

# Initialize PaddleOCR  (load model once)
ocr = PaddleOCR(
    use_doc_unwarping=False,
    use_textline_orientation=True,
    lang='en'
)

# print(f"[TIME] OCR model loading: {time.time() - start:.3f} seconds")

def extract_timetable_from_image(img_path):
    # start = time.time()

    # Run OCR
    results = ocr.predict(img_path)      #scaled_img #img_path

    # print(f"[TIME] OCR prediction: {time.time() - start:.3f} seconds")

    res = results[0]

    # Extract raw entries
    texts = res['rec_texts']
    polys = res['rec_polys']
    raw_entries = [
        {
            "text": text.strip(),
            "box": poly
        }
        for text, poly in zip(texts, polys)
    ]

    # Testing
    # for entry in raw_entries[:5]:
    #     print(entry)

    # Testing
    # for res in results:
    #     res.save_to_img("output")
    #     res.save_to_json("output")

    # Separate time headers (start/end), day labels, and classrooms
    time_row_top = []
    time_row_bottom = []
    day_labels = []
    classroom_cells = []

    # Classify the recognized text
    for entry in raw_entries:
        text = entry['text']
        if (text == 'Physical') or (text == 'Day/Time'):
            continue
        if is_day(text):
            day_labels.append({**entry, 'text': text.capitalize()})
        elif is_time(text):
            _, y = box_center(entry['box'])
            if y < 30:      # Adjust threshold based on image
                time_row_top.append(entry)
            else:
                time_row_bottom.append(entry)
        elif is_classroom_code(text):
            classroom_cells.append(entry)

    # Testing
    # print(time_row_top)
    # print(time_row_bottom)
    # print(day_labels)
    # print(classroom_cells)

    # Step 1: Match time headers
    time_columns = []
    for start in time_row_top:
        sx, _ = box_center(start['box'])
        best_match = None
        for end in time_row_bottom:
            ex, _ = box_center(end['box'])
            if abs(sx - ex) < 20:   # adjust if needed
                best_match = end
                break
        if best_match:
            time_columns.append({
                'start_time': start['text'],
                'end_time': best_match['text'],
                'x': sx
            })

    # Testing
    # print(time_columns)

    # Sort time left to right, day top to bottom
    # time_columns.sort(key=lambda x: x['x'])
    # day_labels.sort(key=lambda d: box_center(d['box'])[1])

    # Step 2: Map each classroom code to day and time
    final_results = []

    for entry in classroom_cells:
        cx, cy = box_center(entry['box'])

        # Match day
        matched_day = None
        for d in day_labels:
            _, dy = box_center(d['box'])

            if cy < dy:
                matched_day = d['text']
                break

        # Match time
        matched_times = []
        for col in time_columns:
            if cx >= col['x'] - 50 and cx <= col['x'] + 60:     # adjust if needed (right and left bound)
                matched_times.append(col)

        if matched_day and matched_times:
            start_time = matched_times[0]['start_time']
            end_time = matched_times[-1]['end_time']
            final_results.append({
                "day": matched_day,
                "start_time": start_time,
                "end_time": end_time,
                "location": entry['text']
            })

    # Output
    return final_results
    # print(json.dumps(final_results))

    # print(f"[TIME] Total execution time: {time.time() - overall_start:.3f} seconds")
