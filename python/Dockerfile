# Usar una imagen base de Python
FROM python:3.11

# Instalar dependencias
RUN apt-get update && apt-get install -y ffmpeg && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar yt-dlp y Selenium para manejar cookies
RUN pip install yt-dlp selenium

# Configurar el directorio de trabajo
WORKDIR /app

# Crear carpeta de videos
RUN mkdir -p /app/videos

# Copiar el script de descarga y cookies
COPY descargar_video.py /app/descargar_video.py
COPY exportar_cookies.py /app/exportar_cookies.py
COPY cookies.txt /app/cookies.txt

# Definir el comando de ejecución
ENTRYPOINT ["python", "/app/descargar_video.py"]
