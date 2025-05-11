import os
import yt_dlp

# Obtener la URL y el nombre del video desde variables de entorno
url = os.getenv("VIDEO_URL")
video_name = os.getenv("VIDEO_NAME")

if not url or not video_name:
    print("Error: No se proporcionó una URL o un nombre de archivo.")
    exit(1)

# Opciones avanzadas de configuración para simular navegador
ydl_opts = {
    'outtmpl': f'/app/videos/{video_name}.mp4',
    'cookies': '/app/cookies.txt',  # Usa cookies exportadas desde el navegador
    'user_agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',  # Simula navegador real
    'referer': 'https://www.youtube.com/',  # Simula acceso desde YouTube
    'headers': {
        'Accept-Language': 'en-US,en;q=0.9',  # Evita bloqueo regional
    },
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
