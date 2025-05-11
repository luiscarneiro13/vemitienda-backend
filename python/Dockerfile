# Usar una imagen base de Python
FROM python:3.11

# Instalar dependencias
RUN apt-get update && apt-get install -y ffmpeg

# Instalar yt-dlp
RUN pip install yt-dlp

# Configurar el directorio de trabajo
WORKDIR /app

# Copiar el script y cookies
COPY descargar_video.py /app/descargar_video.py
COPY cookies.txt /app/cookies.txt

# Definir el comando de ejecución
ENTRYPOINT ["python", "/app/descargar_video.py"]
