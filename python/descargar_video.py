import os
import yt_dlp

# Obtener la URL y el nombre del video
url = os.getenv("VIDEO_URL")
video_name = os.getenv("VIDEO_NAME")

if not url or not video_name:
    print("Error: No se proporcionó una URL o un nombre de archivo.")
    exit(1)

# Opciones de yt-dlp con cookies
ydl_opts = {
    'outtmpl': f'/app/videos/{video_name}.mp4',
    'cookies': '/app/cookies.txt',  # ✅ Usa cookies de YouTube
    'user_agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
    'referer': 'https://www.youtube.com/',
    'format': 'bestvideo[ext=mp4]+bestaudio[ext=m4a]/best',
    'merge_output_format': 'mp4',
}

try:
    with yt_dlp.YoutubeDL(ydl_opts) as ydl:
        ydl.download([url])
    print(f"Descarga completada: /app/videos/{video_name}.mp4")
except Exception as e:
    print("Error al descargar el video:", e)
