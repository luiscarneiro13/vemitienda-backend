#!/bin/sh
set -e

# Rutas donde se guardarán el certificado y la llave
CRT="/etc/ssl/certs/nginx-selfsigned.crt"
KEY="/etc/ssl/private/nginx-selfsigned.key"

# Si no existen, genera un certificado self-signed
if [ ! -f "$CRT" ] || [ ! -f "$KEY" ]; then
    echo "Generando certificado SSL autogenerado..."
    mkdir -p /etc/ssl/certs /etc/ssl/private
    openssl req -x509 -nodes -days 365 \
        -subj "/C=US/ST=State/L=City/O=Organization/OU=Department/CN=localhost" \
        -newkey rsa:2048 \
        -keyout "$KEY" \
        -out "$CRT"
fi

# Inicia Nginx
exec "$@"
