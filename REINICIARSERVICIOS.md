Script Intermedio
La mejor opción es crear un nuevo script que contenga todos los comandos que necesitas ejecutar antes de llamar a start.sh. Esto hace que el servicio de systemd sea más simple y menos propenso a errores.

Paso 1: Crea un script intermedio
Crea un nuevo script llamado startup-sequence.sh en tu directorio de inicio.

Bash

sudo nano /home/vemitiendabackend/startup-sequence.sh
Copia y pega el siguiente código en el archivo:

Bash

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
Guarda y cierra el editor.

Paso 2: Dale permisos de ejecución al nuevo script
Asegúrate de que el script tenga los permisos correctos para poder ser ejecutado.

Bash

sudo chmod +x /home/vemitiendabackend/startup-sequence.sh
Paso 3: Modifica el archivo de servicio de Systemd
Ahora, modifica el archivo docker-startup.service para que apunte a este nuevo script intermedio.

Bash

sudo nano /etc/systemd/system/docker-startup.service
Modifica la sección [Service] para que se vea así:

Ini, TOML

[Unit]
Description=Docker Containers Startup Script
After=docker.service

[Service]
Type=oneshot
ExecStart=/home/vemitiendabackend/startup-sequence.sh
RemainAfterExit=yes

[Install]
WantedBy=multi-user.target
Guarda y cierra el archivo.

Paso 4: Recarga y habilita el servicio
Finalmente, recarga los demonios de systemd para que los cambios surtan efecto y habilita el servicio.

Bash

sudo systemctl daemon-reload
sudo systemctl enable docker-startup.service
Con esta configuración, el servicio de systemd ejecutará el script startup-sequence.sh que a su vez ejecutará todos los comandos que necesitas, incluyendo el start.sh, de una forma ordenada y robusta.