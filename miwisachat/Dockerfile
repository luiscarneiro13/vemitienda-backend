# Etapa 1: build
FROM node:18-alpine AS builder

WORKDIR /app

COPY package.json yarn.lock ./
RUN yarn install --frozen-lockfile

COPY . .

# Etapa 2: producción
FROM node:18-alpine

WORKDIR /app

ENV NODE_ENV=production

COPY --from=builder /app ./
RUN yarn cache clean && rm -rf tests .git

EXPOSE 3977

CMD ["yarn", "start"]
