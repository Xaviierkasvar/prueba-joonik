FROM node:18-alpine AS builder

# Establecer directorio de trabajo
WORKDIR /app

# Instalar dependencias necesarias
RUN apk add --no-cache python3 make g++

# Copiar archivos de configuración primero
COPY package*.json ./

# Instalar dependencias
RUN npm install

# Copiar el resto del código
COPY . .

# Configurar el puerto para React
ENV PORT=3002

# Build
RUN npm run build

# Etapa de producción
FROM node:18-alpine

# Establecer directorio de trabajo
WORKDIR /app

COPY --from=builder /app/node_modules ./node_modules
COPY --from=builder /app/package.json ./package.json
COPY --from=builder /app/build ./build
COPY --from=builder /app/public ./public

# Configurar el puerto para React
ENV PORT=3002

# Exponer puerto 3002
EXPOSE 3002

# Comando para iniciar la aplicación
CMD ["npm", "start"]
