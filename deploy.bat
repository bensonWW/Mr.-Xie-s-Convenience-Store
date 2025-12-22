@echo off
REM ===========================================
REM Mr. Xie's Convenience Store - Deploy Script (Windows)
REM ===========================================

setlocal enabledelayedexpansion

echo =========================================
echo   Mr. Xie's Convenience Store Deployer
echo =========================================

set COMPOSE_FILE=docker-compose.prod.yml
set BUILD_FRONTEND=1
set RUN_MIGRATIONS=1

REM Parse arguments
:parse_args
if "%~1"=="" goto :start_deploy
if "%~1"=="--dev" set COMPOSE_FILE=docker-compose.yml
if "%~1"=="--no-frontend" set BUILD_FRONTEND=0
if "%~1"=="--no-migrate" set RUN_MIGRATIONS=0
shift
goto :parse_args

:start_deploy
echo Using compose file: %COMPOSE_FILE%

REM Step 1: Build frontend
if %BUILD_FRONTEND%==1 (
    echo.
    echo [1/5] Building frontend...
    cd xie_vue
    call npm ci --legacy-peer-deps
    call npm run build
    cd ..
) else (
    echo.
    echo [1/5] Skipping frontend build
)

REM Step 2: Stop existing containers
echo.
echo [2/5] Stopping existing containers...
docker-compose -f %COMPOSE_FILE% down --remove-orphans

REM Step 3: Build and start containers
echo.
echo [3/5] Building and starting containers...
docker-compose -f %COMPOSE_FILE% up -d --build

REM Step 4: Wait for database
echo.
echo [4/5] Waiting for database...
timeout /t 15 /nobreak

REM Step 5: Run migrations
if %RUN_MIGRATIONS%==1 (
    echo.
    echo [5/5] Running migrations...
    docker exec mr-xies-app php artisan migrate --force
    docker exec mr-xies-app php artisan config:cache
    docker exec mr-xies-app php artisan route:cache
    docker exec mr-xies-app php artisan view:cache
) else (
    echo.
    echo [5/5] Skipping migrations
)

echo.
echo =========================================
echo   Deployment Complete!
echo =========================================
echo.
echo Container Status:
docker-compose -f %COMPOSE_FILE% ps
echo.
echo Application is running at:
echo   Frontend: http://localhost
echo   API:      http://localhost/api

endlocal
