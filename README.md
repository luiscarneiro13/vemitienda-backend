# CRM-WEB
Este es un repositorio con la configuración inicial para dockerizar un laravel 7. Se crean 3 contenedores y estos se manejan dentro de la misma subred

  **1.- Contenedor nginx**
  **2.- Contenedor php**
  **3.- Contenedor mysql**

Se debe tener docker y (docker-compose 1.27.0 o sup)

# Instalación en Entorno local


    **Instalar el entorno:**
  
        
        docker compose up -d --build 


    **Crear archivo .env dentro de ubuntu, no en el container**



    **Instalar el proyecto**

   
        docker compose exec php composer install

  
        docker compose run --rm artisan migrate --seed
        

  Si sale el siguiente error, se debe tomar la dirección que está despues del @ y colocarla en la variable DB_HOST del .env (En este caso quedaría así: DB_HOST=172.18.0.3):

  SQLSTATE[HY000] [1045] Access denied for user 'crm-web'@'172.18.0.3' (using password: YES)
  

# Instalación en entorno de Producción

  Crear droplet digitaocean con ubuntu 18.04. Este es un droplet que no trae LAMP no LEMP ni  nada, es un droplet vacío.

  **Acceder al droplet por ssh y ejecutar:**

  apt-get update

  **Instalación de Docker**

  sudo apt install apt-transport-https ca-certificates curl software-properties-common

  curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -

  sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu bionic stable"

  sudo apt update

  apt-cache policy docker-ce

  sudo apt install docker-ce

  sudo systemctl status docker

  sudo usermod -aG docker ${USER}

  sudo apt-get update

  **Instalación de Doker Compose**

  $ sudo curl -L https://github.com/docker/compose/releases/download/1.21.2/docker-compose-`uname -s`-`uname -m` -o /usr/local/bin/docker-compose

  sudo chmod +x /usr/local/bin/docker-compose

  docker-compose --version

  **Clonar el repositorio:**

  git clone https://github.com/luiscarneiro13/crm-web.git

  **Dirigirse a la carpeta del proyecto:**

  cd crm-web

  **Instalar el entorno de docker para producción:**
  
  docker-compose -f docker-compose.prod.yml up -d --build

  **Dar permiso a Carpetas:**

  cd src
  
  sudo chmod 777 -R storage

  **Crear archivo .env con las siguientes variables**
  
  DB_CONNECTION=mysql

  DB_HOST=mysql

  DB_PORT=3306

  DB_DATABASE=homestead

  DB_USERNAME=homestead

  DB_PASSWORD=nn#~y}&D%4/[;/J:2yJA

  **Instalar el proyecto**

  docker-compose run --rm composer install
  
  docker-compose run --rm artisan migrate --seed

  Si sale el siguiente error, se debe tomar la dirección que está despues del @ y colocarla en la variable DB_HOST del .env (En este caso quedaría así: DB_HOST=172.18.0.3):

  SQLSTATE[HY000] [1045] Access denied for user 'crm-web'@'172.18.0.3' (using password: YES)
  