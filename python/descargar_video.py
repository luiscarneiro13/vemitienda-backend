import os
import yt_dlp

# Obtener la URL y el nombre personalizado
url = os.getenv("VIDEO_URL")
video_name = os.getenv("VIDEO_NAME")

if not url or not video_name:
    print("Error: No se proporcionó una URL o un nombre de archivo.")
    exit(1)

# Opciones de descarga
ydl_opts = {
    'outtmpl': f'/app/videos/{video_name}.mp4',
    'cookies': '/app/cookies.txt',  # Usa el archivo de cookies
    'format': 'bestvideo[ext=mp4][height<=480]+bestaudio[ext=m4a]/best[ext=mp4]',
    'merge_output_format': 'mp4',
}


# Descargar el video
try:
    with yt_dlp.YoutubeDL(ydl_opts) as ydl:
        ydl.download([url])
    print(f"Descarga completada: /app/videos/{video_name}.mp4")
except Exception as e:
    print("Error al descargar el video:", e)
