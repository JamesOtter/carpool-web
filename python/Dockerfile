# Use an official Python base image
FROM python:3.10-slim

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libgl1-mesa-glx \
    libglib2.0-0 \
    libgomp1 \
    && apt-get clean

# Set working directory inside the container
WORKDIR /app

# Copy files
COPY . /app

# Copy your script and any required files into the container
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Run FastAPI server
CMD ["uvicorn", "app:app", "--host", "0.0.0.0", "--port", "80"]
