#!/bin/bash

echo "🔻 Deteniendo contenedores del proyecto..."
docker stop vemitiendabackend-php vemitiendabackend-nginx miwisachat-node 2>/dev/null || true

echo "🧺 Eliminando contenedores detenidos..."
docker container prune -f

echo "⏳ Esperando un momento para completar limpieza..."
sleep 2

echo "📌 Contenedores detenidos restantes:"
docker ps -a -q -f "status=exited" | grep . \
  && echo "❗ Algunos contenedores no se eliminaron correctamente" && exit 1 \
  || echo "✅ Todos los contenedores detenidos fueron eliminados"

echo "📂 Posicionándose en el proyecto y desplegando..."
cd /home/vemitiendabackend &&
git checkout . &&
git pull &&
chmod +x start.sh &&
chmod +x deploy-on-reboot.sh &&
./start.sh
chmod +x renovar_cert.sh &&
./renovar_cert.sh