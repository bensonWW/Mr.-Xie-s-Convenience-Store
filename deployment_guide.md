# Deployment Guide - Mr. Xie's Convenience Store

This guide outlines the steps to deploy the application to a production environment using Docker.

## Prerequisites
- Docker & Docker Compose installed on the host machine.
- Git (to pull the repository).
- A domain name (optional, but recommended for SSL).

## 1. Environment Configuration
1.  **Copy `.env.example` to `.env`**:
    ```bash
    cp .env.example .env
    ```
2.  **Update Production Values**:
    *   `APP_ENV`: Set to `production`
    *   `APP_DEBUG`: Set to `false`
    *   `APP_URL`: Your production URL (e.g., `https://store.example.com`)
    *   `DB_PASSWORD`: Set a strong password.
    *   `MEILISEARCH_KEY`: Set a strong master key.

## 2. Frontend Build
The frontend is a Vue SPA. For production, we build static files and serve them via Nginx.
(Note: Our Docker setup handles this automatically if using the production Dockerfile, but for manual verification:)
```bash
cd xie_vue
npm install
npm run build
# The 'dist/' directory now contains the production-ready assets.
```

## 3. Docker Deployment
We use `docker-compose` to orchestrate services:
*   `app`: Laravel API (PHP-FPM)
*   `web`: Nginx (Serves API & Frontend)
*   `db`: MySQL 8.0
*   `redis`: Cache & Queue
*   `meilisearch`: Search Engine

### Running in Production
```bash
# Build and Start Containers (Detached mode)
docker-compose up -d --build

# Run Migrations (First time only)
docker-compose exec app php artisan migrate --seed

# Optimize Backend (Critical for speed)
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

## 4. Verification
1.  Visit your URL (e.g., `http://localhost:8080`).
2.  Check API Health: `http://localhost:8080/api/settings` should return JSON.
3.  Test Login: Ensure you can log in as Admin.

## 5. Maintenance
*   **Logs**: `docker-compose logs -f app`
*   **Updates**: `git pull && docker-compose build && docker-compose up -d`
