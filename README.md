# Proyecto de Prueba Técnica con Laravel y React

Este proyecto demuestra una aplicación full-stack con un backend en Laravel y un frontend en React usando TypeScript. Incluye autenticación por API Key e implementa Material UI para el frontend.

## Estructura del Proyecto

El proyecto está dividido en dos partes principales:

```
laravel-react-locations/
├── backend/               # Backend Laravel
│   ├── app/               # Código principal de la aplicación
│   │   ├── Http/          # Controladores y middleware
│   │   ├── Models/        # Modelos de datos
│   │   └── Services/      # Servicios para lógica de negocio
│   ├── database/          # Migraciones y seeders
│   ├── routes/            # Definición de rutas API
│   ├── tests/             # Pruebas automatizadas
│   └── .env               # Variables de entorno
├── frontend/              # Frontend React
│   ├── public/            # Archivos estáticos
│   ├── src/               # Código fuente React
│   └── package.json       # Dependencias
├── docker/                # Configuración Docker
│   └── nginx/             # Configuración de Nginx
├── docker-compose.yml     # Configuración de servicios Docker
└── README.md              # Documentación del proyecto
```

## Configuración con Docker

### Requisitos
- Docker
- Docker Compose

### Instrucciones para ejecutar con Docker

1. Clona el repositorio:
   ```bash
   git clone https://github.com/Xaviierkasvar/prueba-joonik.git
   cd backend
   ```

2. Configura el archivo .env.docker en el directorio backend:
   ```bash
   cp backend/.env.example backend/.env.docker
   ```
   Asegúrate de configurar correctamente las variables de entorno, especialmente las relacionadas con la base de datos.

3. Construye e inicia los contenedores:
   ```bash
   docker-compose up -d --build
   ```

4. Genera una clave de aplicación para Laravel:
   ```bash
   docker-compose exec backend php artisan key:generate --env=docker
   ```

5. Ejecuta las migraciones y los seeders:
   ```bash
   docker-compose exec backend php artisan migrate:fresh --seed
   ```

## Acceso a la Aplicación

Una vez que los contenedores estén funcionando, podrás acceder a la aplicación en:

- Frontend: http://localhost:8000
- Backend API: http://localhost:8000/api

### Comandos Útiles

- Acceder al contenedor del backend:
  ```bash
  docker-compose exec backend bash
  ```

- Acceder al contenedor del frontend:
  ```bash
  docker-compose exec frontend bash
  ```

- Ver los logs de todos los contenedores:
  ```bash
  docker-compose logs -f
  ```

### Comandos Útiles de Docker

- Ver los contenedores en ejecución:
  ```bash
  docker-compose ps
  ```

- Ver los logs de los contenedores:
  ```bash
  docker-compose logs -f
  docker-compose logs -f backend
  docker-compose logs -f frontend
  ```

- Detener los contenedores:
  ```bash
  docker-compose down
  ```

- Ejecutar comandos de Artisan:
  ```bash
  docker-compose exec backend php artisan <comando>
  ```

- Acceder a la base de datos:
  ```bash
  docker-compose exec db psql -U postgres -d prueba_joonik_db
  ```

## Autenticación de API

Todos los endpoints de la API están protegidos con autenticación por API Key. La clave de API debe incluirse en los encabezados de la solicitud como:

```
X-API-KEY: locations-api-key-12345
```

## Pruebas

### Pruebas del Backend

Ejecuta las pruebas del backend con:

```
cd backend
php artisan test
```

### Pruebas del Frontend

Ejecuta las pruebas del frontend con:

```
cd frontend
npm test
```

## Características Principales

- **Backend Laravel con API RESTful**
- **Frontend React con TypeScript**
- **Autenticación por API Key**
- **Interfaz de usuario con Material UI**
- **Dockerización completa del proyecto**
- **Servicios separados para backend, frontend y base de datos**
