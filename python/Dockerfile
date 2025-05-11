# Usar una imagen base de Python
FROM python:3.11

# Actualizar paquetes del sistema
RUN apt-get update && apt-get install -y \
    wget \
    curl \
    unzip \
    ffmpeg \
    chromium \
    chromium-driver \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar dependencias de Python
RUN pip install --no-cache-dir \
    yt-dlp \
    selenium

# Configurar el directorio de trabajo
WORKDIR /app

# Crear carpeta de videos
RUN mkdir -p /app/videos

# Copiar scripts y cookies
COPY descargar_video.py /app/descargar_video.py
COPY exportar_cookies.py /app/exportar_cookies.py
COPY cookies.txt /app/cookies.txt

# Definir comando de ejecución
ENTRYPOINT ["python", "/app/descargar_video.py"]

