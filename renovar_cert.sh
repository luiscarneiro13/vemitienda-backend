#!/bin/bash

# 1. Detener el contenedor de Nginx
docker compose -f docker-compose.prod.yml stop nginx

# 2. Ejecutar la renovación con certbot-docker (usando el método webroot)
#    Asegúrate de cambiar /ruta/absoluta/a/vemitienda por la ruta real en tu VPS.
#    Asegúrate de que esta ruta es la que usa Nginx para el directorio root.
docker run -it --rm \
  -v /etc/letsencrypt:/etc/letsencrypt \
  -v /var/lib/letsencrypt:/var/lib/letsencrypt \
  -v /home/vemitienda:/var/www/html \
  certbot/certbot \
  renew --webroot -w /var/www/html

# 3. Reiniciar el contenedor de Nginx
docker compose -f docker-compose.prod.yml start nginx