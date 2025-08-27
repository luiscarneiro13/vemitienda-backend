#!/bin/bash

# Detiene los contenedores
echo "✅ Deteniendo contenedores..."
docker stop vemitiendabackend-php vemitiendabackend-nginx miwisachat-node 2>/dev/null || true

# Elimina los contenedores detenidos
echo "✅ Eliminando contenedores detenidos..."
docker container prune -f
sleep 2

docker ps -a -q -f "status=exited" | grep . \
  && echo "❗ Algunos contenedores no se eliminaron correctamente" && exit 1 \
  || echo "✅ Todos los contenedores detenidos fueron eliminados"

# Ejecuta el script de inicio principal
echo "📂 Ejecutando el script de inicio principal..."
cd /home/vemitiendabackend &&
/home/vemitiendabackend/start.sh





# Recordar darle permisos de ejecucion a este script: sudo chmod +x /home/vemitiendabackend/startup-sequence.sh
# Se usa para que levante el proyecto despues de reiniciar el servidor completamente