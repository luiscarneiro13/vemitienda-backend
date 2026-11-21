Actúa como un arquitecto senior especializado en Laravel SaaS, DevOps y arquitectura escalable.

Necesito construir un proyecto privado llamado:

Laravel Starter

El objetivo es crear una plantilla reutilizable para desarrollar rápidamente nuevos sistemas administrativos, ERP, CRM y aplicaciones SaaS.

Esta plantilla debe eliminar el trabajo repetitivo de crear:

- autenticación
- usuarios
- roles
- permisos
- panel administrativo
- auditoría
- multitenancy
- configuración inicial
- APIs

Cada nuevo software debe poder comenzar clonando este proyecto.

=============================
ESTRUCTURA DEL REPOSITORIO
=============================

El proyecto debe respetar exactamente esta estructura:


delivery/

├── .github/

├── docs/

├── nginx/

│   └── default.conf


├── services/

│   └── laravel/

│       ├── Dockerfile

│       ├── app/

│       ├── bootstrap/

│       ├── config/

│       ├── database/

│       ├── public/

│       ├── resources/

│       ├── routes/

│       ├── storage/

│       └── artisan


├── docker-compose.yml

├── docker-compose.prod.yml

├── .env

├── .env.example

├── README.md

├── CONTRIBUTING.md


No deben existir:

- vemitienda/
- miwisachat/


=============================
TECNOLOGÍAS OBLIGATORIAS
=============================


Backend:

- Laravel 12
- PHP 8.3+
- MySQL 8


Paquetes:


Autenticación:

Laravel Breeze


Panel administrativo:

FilamentPHP


Roles y permisos:

Spatie Laravel Permission


Generación automática de permisos:

Filament Shield


API:

Laravel Sanctum


Auditoría:

Laravel Auditing



=============================
DOCKER
=============================


Todo debe ejecutarse mediante Docker.


No depender de instalaciones locales.


Servicios obligatorios:


1) Laravel Application


Nombre servicio:

delivery-app


Container:

delivery-app


Debe utilizar:


services/laravel/Dockerfile


Debe ejecutar:

PHP-FPM


Debe incluir:

- PHP 8.3
- Composer
- extensiones Laravel necesarias
- extensiones MySQL


Volumen:


./services/laravel:/var/www



=============================


2) Queue Worker


Nombre:

delivery-worker


Container:

delivery-worker


Debe ejecutar:


php artisan queue:work --tries=3 --sleep=3



=============================


3) Nginx


Nombre:

delivery-nginx


Container:

delivery-nginx


Imagen:

nginx:stable-alpine


Debe usar:


nginx/default.conf



Puerto:

8000:80



=============================


4) MySQL


Nombre:

delivery-mysql


Container:

delivery-mysql


Imagen:

mysql:8.0


Base:

delivery


Debe utilizar volumen:


mysql_data



Debe incluir healthcheck.


=============================


RED DOCKER


Crear:


delivery-network


Todos los servicios deben comunicarse mediante esta red.


Laravel debe usar:


DB_HOST=delivery-mysql



=============================
LARAVEL
=============================


Implementar:


Laravel Breeze:


- Login
- Registro
- Logout
- Recuperación contraseña
- Verificación email
- Perfil usuario
- Cambio contraseña



=============================
FILAMENT ADMIN
=============================


Crear panel:

/admin


Debe incluir:


Dashboard

Usuarios

Roles

Permisos

Empresas

Auditoría

Logs

Configuración

Notificaciones

Facturación



=============================
MULTITENANCY
=============================


Preparar arquitectura multitenant desde el inicio.


Crear:


Company


User


CompanyUser


Todas las entidades deben estar preparadas para asociarse a una empresa.


Implementar scopes para separar información por tenant.



=============================
ROLES Y PERMISOS
=============================


Implementar:

Spatie Permission


Roles iniciales:


Super Admin

Admin

Manager

User



Crear permisos:


users.view

users.create

users.update

users.delete


roles.view

roles.create

roles.update

roles.delete


permissions.view

permissions.create

permissions.update

permissions.delete


companies.view

companies.create

companies.update

companies.delete


settings.view

settings.update



Super Admin debe tener todos los permisos.



=============================
AUDITORÍA
=============================


Implementar Laravel Auditing.


Registrar:


Login

Logout

Creación

Actualización

Eliminación


Crear pantalla Filament:


Auditoría


Mostrar:


Usuario

Acción

Modelo

Fecha

IP

Valores anteriores

Valores nuevos



=============================
MÓDULOS BASE
=============================


Crear:


Usuarios


Roles


Permisos


Auditoría


Configuración global


Notificaciones


Logs del sistema


API Tokens


Perfil usuario


Dashboard estadísticas


Empresas / Clientes


Facturación / Suscripciones



=============================
BASE DE DATOS
=============================


Crear migraciones.


Crear seeders.


Ejecutando:


php artisan migrate --seed


Debe crear:


Usuario administrador:


Email:

admin@example.com


Password:

password



Roles

Permisos

Configuración inicial



=============================
CALIDAD DEL CÓDIGO
=============================


Aplicar:


SOLID

Laravel Best Practices

Service Layer

Form Requests

Policies

Clean Code


No colocar lógica de negocio en Controllers.



=============================
DOCUMENTACIÓN
=============================


Crear:


README.md


Debe explicar:


Instalación Docker

Configuración .env

Migraciones

Usuario inicial

Crear módulos nuevos

Uso de Filament

Uso de Shield

Uso de permisos


Crear:


docs/


installation.md

docker.md

architecture.md

permissions.md



=============================
OBJETIVO FINAL
=============================


Al ejecutar:


docker compose up -d


debe existir:


✓ Laravel funcionando

✓ MySQL funcionando

✓ Queue funcionando

✓ Nginx funcionando

✓ Filament en /admin

✓ Usuarios

✓ Roles

✓ Permisos

✓ Auditoría

✓ Multitenancy

✓ API preparada

✓ Base reutilizable para nuevos SaaS


Este proyecto será una plantilla privada profesional para crear software rápidamente.