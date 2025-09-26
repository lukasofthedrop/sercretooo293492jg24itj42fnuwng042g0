#!/bin/bash

# LucrativaBet Railway Deployment Script
# This script helps deploy the application to Railway

echo "=== LucrativaBet Railway Deployment Script ==="

# Check if Railway CLI is installed
if ! command -v railway &> /dev/null; then
    echo "Railway CLI is not installed. Installing..."
    npm install -g @railway/cli
fi

# Check if user is logged in to Railway
if ! railway whoami &> /dev/null; then
    echo "Please login to Railway:"
    railway login
fi

# Initialize Railway project if not already initialized
if [ ! -f "railway.toml" ]; then
    echo "Initializing Railway project..."
    railway init
fi

# Link to existing Railway project or create new one
echo "Linking to Railway project..."
railway link

# Set environment variables
echo "Setting environment variables..."
railway variables:set APP_ENV="production"
railway variables:set APP_DEBUG="false"
railway variables:set APP_URL="${RAILWAY_PUBLIC_DOMAIN}"
railway variables:set LOG_CHANNEL="stack"
railway variables:set LOG_LEVEL="error"
railway variables:set CACHE_DRIVER="redis"
railway variables:set SESSION_DRIVER="redis"
railway variables:set QUEUE_CONNECTION="redis"
railway variables:set SESSION_SECURE_COOKIE="true"
railway variables:set SESSION_ENCRYPT="true"
railway variables:set FORCE_HTTPS="true"

# Set security keys (these should be properly generated for production)
railway variables:set JWT_SECRET="jwt_secure_secret_key_2025_generated_for_production"
railway variables:set PUSHER_APP_SECRET="pusher_secure_secret_key_2025_generated"
railway variables:set GOOGLE_API_SECRET="google_api_secret_2025_generated_secure"
railway variables:set STRIPE_SECRET="stripe_api_secret_2025_generated_secure"
railway variables:set STRIPE_WEBHOOK_SECRET="stripe_webhook_secret_2025_generated"

# Set Pusher configuration
railway variables:set PUSHER_APP_ID="6138611"
railway variables:set PUSHER_APP_KEY="jSL647jlthPQkQLyfkDTwmNzkNB5s="
railway variables:set PUSHER_HOST="credkbet.local"
railway variables:set PUSHER_PORT="6001"
railway variables:set PUSHER_SCHEME="http"
railway variables:set PUSHER_APP_CLUSTER="mt1"

# Set Google API
railway variables:set GOOGLE_API_KEY="506408718143-pt505a3la8pktgda0o6624m4hr0nqca7.apps.googleusercontent.com"

# Set Stripe configuration
railway variables:set STRIPE_ENABLED="on"
railway variables:set STRIPE_SUBSCRIPTION_ENABLED="on"
railway variables:set STRIPE_BASE_URI="https://api.stripe.com"
railway variables:set STRIPE_KEY="OndaGames.com"

# Set mail configuration
railway variables:set MAIL_MAILER="smtp"
railway variables:set MAIL_HOST="sandbox.smtp.mailtrap.io"
railway variables:set MAIL_PORT="2525"
railway variables:set MAIL_USERNAME="cd7e378cb24f2d"
railway variables:set MAIL_PASSWORD="1f58d53cf1ddfb"
railway variables:set MAIL_ENCRYPTION="tls"
railway variables:set MAIL_FROM_ADDRESS="hello@example.com"
railway variables:set MAIL_FROM_NAME="Lucrativa Bet"

# Set 2FA token
railway variables:set TOKEN_DE_2FA="000000"

# Add MySQL service
echo "Adding MySQL service..."
railway add mysql --name="lucrativabet-mysql"

# Add Redis service
echo "Adding Redis service..."
railway add redis --name="lucrativabet-redis"

# Add persistent storage
echo "Adding persistent storage..."
railway volume add --name="lucrativabet-storage" --mountPath="/app/storage/app/public" --size="1GB"

# Update railway.toml to use Railway-specific Dockerfile
echo "Updating railway.toml configuration..."
cat > railway.toml << 'EOF'
# Railway Configuration for LucrativaBet Casino Application
# This file defines the services, build commands, and deployment settings

[build]
command = """
    composer install --no-dev --optimize-autoloader
    php artisan key:generate
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan migrate --force
    php artisan storage:link
"""

[deploy]
startCommand = "php artisan serve --host=0.0.0.0 --port=$PORT"
restartPolicyType = "ON_FAILURE"
restartPolicyMaxRetries = 3

# Main Web Application Service
[service.web]
    # Service settings
    name = "lucrativabet-web"
    memory = "512MB"
    cpu = "0.5"
    
    # Docker configuration
    dockerfilePath = "Dockerfile.railway"
    dockerContext = "."
    
    # Build and deployment
    buildCommand = """
        composer install --no-dev --optimize-autoloader
        php artisan key:generate
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        php artisan migrate --force
        php artisan storage:link
    """
    
    startCommand = "php artisan serve --host=0.0.0.0 --port=$PORT"
    
    # Environment variables for Railway
    [service.web.env]
        APP_ENV = "production"
        APP_DEBUG = "false"
        APP_URL = "${RAILWAY_PUBLIC_DOMAIN}"
        LOG_CHANNEL = "stack"
        LOG_LEVEL = "error"
        
        # Database configuration (will be provided by Railway MySQL service)
        DB_CONNECTION = "mysql"
        DB_HOST = "${MYSQL.HOST}"
        DB_PORT = "${MYSQL.PORT}"
        DB_DATABASE = "${MYSQL.DATABASE}"
        DB_USERNAME = "${MYSQL.USERNAME}"
        DB_PASSWORD = "${MYSQL.PASSWORD}"
        
        # Redis configuration (will be provided by Railway Redis service)
        REDIS_HOST = "${REDIS.HOST}"
        REDIS_PASSWORD = "${REDIS.PASSWORD}"
        REDIS_PORT = "${REDIS.PORT}"
        CACHE_DRIVER = "redis"
        SESSION_DRIVER = "redis"
        QUEUE_CONNECTION = "redis"
        
        # Security settings
        SESSION_SECURE_COOKIE = "true"
        SESSION_ENCRYPT = "true"
        FORCE_HTTPS = "true"

# MySQL Database Service
[service.mysql]
    name = "lucrativabet-mysql"
    memory = "256MB"
    cpu = "0.25"
    
    # MySQL specific settings
    [service.mysql.env]
        MYSQL_DATABASE = "lucrativabet"
        MYSQL_ROOT_PASSWORD = "railway_secure_password_2025"

# Redis Cache Service
[service.redis]
    name = "lucrativabet-redis"
    memory = "128MB"
    cpu = "0.1"
    
    # Redis specific settings
    [service.redis.env]
        REDIS_PASSWORD = "railway_redis_password_2025"

# Volume for persistent storage
[volume.storage]
    name = "lucrativabet-storage"
    size = "1GB"
    mountPath = "/app/storage/app/public"

# Health check configuration
[healthcheck]
    path = "/health"
    interval = "30s"
    timeout = "10s"
    retries = 3
    startPeriod = "40s"

# Domain configuration
[domain]
    name = "lucrativabet.railway.app"
    scheme = "https"
EOF

# Deploy to Railway
echo "Deploying to Railway..."
railway up

echo "=== Railway deployment complete! ==="
echo "Your application should be available at: ${RAILWAY_PUBLIC_DOMAIN}"
echo "To check deployment status, run: railway status"
echo "To view logs, run: railway logs"