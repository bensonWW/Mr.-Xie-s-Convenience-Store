#!/bin/bash
# ===========================================
# Mr. Xie's Convenience Store - Deploy Script
# ===========================================

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}=========================================${NC}"
echo -e "${GREEN}  Mr. Xie's Convenience Store Deployer  ${NC}"
echo -e "${GREEN}=========================================${NC}"

# Default values
COMPOSE_FILE="docker-compose.prod.yml"
BUILD_FRONTEND=true
RUN_MIGRATIONS=true
CLEAR_CACHE=true

# Parse arguments
while [[ "$#" -gt 0 ]]; do
    case $1 in
        --dev) COMPOSE_FILE="docker-compose.yml" ;;
        --no-frontend) BUILD_FRONTEND=false ;;
        --no-migrate) RUN_MIGRATIONS=false ;;
        --no-cache) CLEAR_CACHE=false ;;
        *) echo "Unknown parameter: $1"; exit 1 ;;
    esac
    shift
done

echo -e "${YELLOW}Using compose file: ${COMPOSE_FILE}${NC}"

# Step 1: Pull latest code (if in git repo)
if [ -d ".git" ]; then
    echo -e "\n${YELLOW}[1/7] Pulling latest code...${NC}"
    git pull origin main || echo "Git pull failed, continuing..."
fi

# Step 2: Build frontend (if enabled)
if [ "$BUILD_FRONTEND" = true ]; then
    echo -e "\n${YELLOW}[2/7] Building frontend...${NC}"
    cd xie_vue
    npm ci --legacy-peer-deps
    npm run build
    cd ..
else
    echo -e "\n${YELLOW}[2/7] Skipping frontend build${NC}"
fi

# Step 3: Stop existing containers
echo -e "\n${YELLOW}[3/7] Stopping existing containers...${NC}"
docker-compose -f "$COMPOSE_FILE" down --remove-orphans

# Step 4: Build and start containers
echo -e "\n${YELLOW}[4/7] Building and starting containers...${NC}"
docker-compose -f "$COMPOSE_FILE" up -d --build

# Step 5: Wait for database to be ready
echo -e "\n${YELLOW}[5/7] Waiting for database...${NC}"
sleep 10
until docker exec mr-xies-db mysqladmin ping -h localhost --silent; do
    echo "Waiting for MySQL..."
    sleep 5
done
echo -e "${GREEN}Database is ready!${NC}"

# Step 6: Run migrations
if [ "$RUN_MIGRATIONS" = true ]; then
    echo -e "\n${YELLOW}[6/7] Running migrations...${NC}"
    docker exec mr-xies-app php artisan migrate --force
else
    echo -e "\n${YELLOW}[6/7] Skipping migrations${NC}"
fi

# Step 7: Clear and optimize cache
if [ "$CLEAR_CACHE" = true ]; then
    echo -e "\n${YELLOW}[7/7] Optimizing application...${NC}"
    docker exec mr-xies-app php artisan config:cache
    docker exec mr-xies-app php artisan route:cache
    docker exec mr-xies-app php artisan view:cache
    docker exec mr-xies-app php artisan event:cache
else
    echo -e "\n${YELLOW}[7/7] Skipping cache optimization${NC}"
fi

# Done!
echo -e "\n${GREEN}=========================================${NC}"
echo -e "${GREEN}  Deployment Complete!                   ${NC}"
echo -e "${GREEN}=========================================${NC}"

# Show container status
echo -e "\n${YELLOW}Container Status:${NC}"
docker-compose -f "$COMPOSE_FILE" ps

echo -e "\n${GREEN}Application is running at:${NC}"
echo -e "  Frontend: http://localhost"
echo -e "  API:      http://localhost/api"
