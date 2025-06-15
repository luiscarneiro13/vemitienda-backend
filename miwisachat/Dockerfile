# Usamos una imagen base de Node.js
FROM node:18-alpine

# Crear directorio de trabajo
WORKDIR /app

# Copiar package.json y package-lock.json
COPY package*.json ./

# Instalar dependencias
RUN npm install

# Copiar el resto del código
COPY . .

# Exponer el puerto donde corre la app (ajústalo si es otro)
EXPOSE 3000

# Comando para iniciar la app
CMD [ "npm", "start" ]
