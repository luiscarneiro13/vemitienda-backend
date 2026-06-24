FROM node:18-alpine

# ffmpeg y ffprobe para procesamiento de video (incluye thumbnails)
RUN apk add --no-cache ffmpeg

# Evita reconstrucciones innecesarias por orden de capas
WORKDIR /app

# Copiar solo lo necesario para instalar dependencias
COPY package.json yarn.lock ./

# Usamos yarn con frozen-lockfile para builds consistentes
RUN yarn install --frozen-lockfile

# Copiamos solo lo necesario
COPY . .

# Establecer variables para reducir tamaño y mejorar seguridad
ENV NODE_ENV=production

# Elimina archivos temporales y desarrollo
RUN yarn cache clean

# Expone el puerto real de la app
EXPOSE 3977

CMD ["yarn", "start"]
