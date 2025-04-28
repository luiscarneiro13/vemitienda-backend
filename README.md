# CRM-WEB
Este es un repositorio con la configuración inicial para dockerizar un laravel 7. Se crean 3 contenedores y estos se manejan dentro de la misma subred

  **1.- Contenedor nginx**
  **2.- Contenedor php**
  **3.- Contenedor mysql**

Se debe tener docker y (docker-compose 1.27.0 o sup)

# Instalación en Entorno local

  **Clonar el repositorio: **

  <code>git clone https://github.com/luiscarneiro13/crm-web.git</code>

  **Dirigirse a la carpeta del proyecto:**

  <code>cd crm-web</code>

  **Instalar el entorno:**
  
  <code>docker compose up -d --build</code>

  **Dirigirse a la carpeta src**
  
  <code>cd src</code>

  **Crear archivo .env**

  **Instalar el proyecto**

  <code>docker compose exec php composer install</code>
  
  <code>docker compose run --rm artisan migrate --seed</code>

  Si sale el siguiente error, se debe tomar la dirección que está despues del @ y colocarla en la variable DB_HOST del .env (En este caso quedaría así: DB_HOST=172.18.0.3):

  SQLSTATE[HY000] [1045] Access denied for user 'crm-web'@'172.18.0.3' (using password: YES)
  

# Instalación en entorno de Producción

  Crear droplet digitaocean con ubuntu 18.04. Este es un droplet que no trae LAMP no LEMP ni  nada, es un droplet vacío.

  **Acceder al droplet por ssh y ejecutar:**

  <code>apt-get update</code>

  **Instalación de Docker**

  <code>sudo apt install apt-transport-https ca-certificates curl software-properties-common</code>

  <code>curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -</code>

  <code>sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu bionic stable"</code>

  <code>sudo apt update</code>

  <code>apt-cache policy docker-ce</code>

  <code>sudo apt install docker-ce</code>

  <code>sudo systemctl status docker</code>

  <code>sudo usermod -aG docker ${USER}</code>

  <code>sudo apt-get update</code>

  **Instalación de Doker Compose**

  $ sudo curl -L https://github.com/docker/compose/releases/download/1.21.2/docker-compose-`uname -s`-`uname -m` -o /usr/local/bin/docker-compose

  <code>sudo chmod +x /usr/local/bin/docker-compose</code>

  <code>docker-compose --version</code>

  **Clonar el repositorio:**

  <code>git clone https://github.com/luiscarneiro13/crm-web.git</code>

  **Dirigirse a la carpeta del proyecto:**

  <code>cd crm-web</code>

  **Instalar el entorno de docker para producción:**
  
  <code>docker-compose -f docker-compose.prod.yml up -d --build</code>

  **Dar permiso a Carpetas:**

  <code>cd src</code>
  
  <code>sudo chmod 777 -R storage</code>

  **Crear archivo .env con las siguientes variables**
  
  <code>DB_CONNECTION=mysql</code>

  <code>DB_HOST=mysql</code>

  <code>DB_PORT=3306</code>

  <code>DB_DATABASE=homestead</code>

  <code>DB_USERNAME=homestead</code>

  <code>DB_PASSWORD=nn#~y}&D%4/[;/J:2yJA</code>

  **Instalar el proyecto**

  <code>docker-compose run --rm composer install</code>
  
  <code>docker-compose run --rm artisan migrate --seed</code>

  Si sale el siguiente error, se debe tomar la dirección que está despues del @ y colocarla en la variable DB_HOST del .env (En este caso quedaría así: DB_HOST=172.18.0.3):

  SQLSTATE[HY000] [1045] Access denied for user 'crm-web'@'172.18.0.3' (using password: YES)
  