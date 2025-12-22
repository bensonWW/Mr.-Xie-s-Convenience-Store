# ===========================================
# Stage 1: Build Stage
# ===========================================
FROM node:22-alpine AS build-stage

WORKDIR /app

# Copy package files first for better caching
COPY package*.json ./

# Install dependencies
RUN npm ci --legacy-peer-deps

# Copy source code
COPY . .

# Build argument for API URL
ARG VITE_API_URL=http://localhost:8000/api
ARG VUE_APP_API_URL=http://localhost:8000/api
ENV VITE_API_URL=$VITE_API_URL
ENV VUE_APP_API_URL=$VUE_APP_API_URL

# Build production bundle
RUN npm run build

# ===========================================
# Stage 2: Production Stage
# ===========================================
FROM nginx:stable-alpine AS production-stage

# Copy built assets
COPY --from=build-stage /app/dist /usr/share/nginx/html

# Copy nginx configuration
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Add health check
HEALTHCHECK --interval=30s --timeout=5s --start-period=5s --retries=3 \
    CMD wget --no-verbose --tries=1 --spider http://localhost/ || exit 1

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
