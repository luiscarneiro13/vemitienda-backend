#!/bin/bash

echo "🔄 Renovando certificados SSL con Certbot..."
docker compose -f docker-compose.prod.yml exec certbot certbot renew --webroot -w /var/www/html

echo "🔄 Recargando Nginx para aplicar nuevos certificados..."
docker exec vemitiendabackend-nginx nginx -s reload

echo "✅ Certificados renovados y Nginx recargado"