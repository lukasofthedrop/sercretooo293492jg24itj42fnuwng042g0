# LucrativaBet Railway Deployment Guide

This guide provides comprehensive instructions for deploying the LucrativaBet Casino application to Railway.

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Project Structure](#project-structure)
3. [Configuration Files](#configuration-files)
4. [Deployment Methods](#deployment-methods)
5. [Environment Variables](#environment-variables)
6. [Services Configuration](#services-configuration)
7. [MCP Integration](#mcp-integration)
8. [Monitoring and Maintenance](#monitoring-and-maintenance)
9. [Troubleshooting](#troubleshooting)

## Prerequisites

Before deploying to Railway, ensure you have:

1. **Railway Account**: Sign up at [railway.app](https://railway.app)
2. **Railway CLI**: Install the Railway CLI
   ```bash
   npm install -g @railway/cli
   ```
3. **Git Repository**: Your project should be in a Git repository
4. **Domain Name**: Custom domain (optional)

## Project Structure

The project has been optimized for Railway deployment with the following structure:

```
lucrativabet-1/
├── .env                           # Environment variables
├── .env.example                   # Environment template
├── railway.toml                   # Railway configuration
├── Dockerfile.railway            # Railway-specific Dockerfile
├── docker-entrypoint-railway.sh  # Railway entrypoint script
├── deploy-railway.sh             # Railway deployment script
├── railway-mcp.json              # Railway MCP configuration
├── railway-mcp.js                # Railway MCP integration
├── backups/                       # Database backups
├── app/                          # Laravel application
├── public/                       # Public files
├── storage/                      # Storage directory
└── vendor/                       # Composer dependencies
```

## Configuration Files

### railway.toml

The `railway.toml` file defines the services, build commands, and deployment settings:

```toml
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

[service.web]
    name = "lucrativabet-web"
    memory = "512MB"
    cpu = "0.5"
    dockerfilePath = "Dockerfile.railway"
    dockerContext = "."
```

### Dockerfile.railway

Optimized Dockerfile for Railway deployment:

```dockerfile
FROM php:8.2-apache
# Install dependencies and extensions
# Configure Apache
# Set permissions
# Install Composer dependencies
```

### docker-entrypoint-railway.sh

Entrypoint script for Railway deployment:

```bash
#!/bin/bash
set -e

echo "=== LucrativaBet Railway Deployment Initialization ==="

# Wait for services
# Run migrations
# Cache configuration
# Set permissions
# Start Apache
```

## Deployment Methods

### Method 1: Using Railway CLI (Recommended)

1. **Login to Railway**:
   ```bash
   railway login
   ```

2. **Initialize Project**:
   ```bash
   railway init
   ```

3. **Link to Existing Project**:
   ```bash
   railway link
   ```

4. **Deploy**:
   ```bash
   railway up
   ```

### Method 2: Using Automated Script

1. **Run the deployment script**:
   ```bash
   chmod +x deploy-railway.sh
   ./deploy-railway.sh
   ```

### Method 3: Using Git Integration

1. **Connect Railway to your Git repository**
2. **Configure environment variables in Railway dashboard**
3. **Trigger deployment by pushing to the connected branch**

## Environment Variables

### Required Variables

| Variable | Description | Example Value |
|----------|-------------|---------------|
| `APP_ENV` | Application environment | `production` |
| `APP_DEBUG` | Debug mode | `false` |
| `APP_URL` | Application URL | `https://lucrativabet.railway.app` |
| `APP_KEY` | Laravel application key | `base64:...` |
| `DB_CONNECTION` | Database connection type | `mysql` |
| `DB_HOST` | Database host | `${MYSQL.HOST}` |
| `DB_PORT` | Database port | `${MYSQL.PORT}` |
| `DB_DATABASE` | Database name | `${MYSQL.DATABASE}` |
| `DB_USERNAME` | Database username | `${MYSQL.USERNAME}` |
| `DB_PASSWORD` | Database password | `${MYSQL.PASSWORD}` |
| `REDIS_HOST` | Redis host | `${REDIS.HOST}` |
| `REDIS_PASSWORD` | Redis password | `${REDIS.PASSWORD}` |
| `REDIS_PORT` | Redis port | `${REDIS.PORT}` |

### Security Variables

| Variable | Description | Example Value |
|----------|-------------|---------------|
| `JWT_SECRET` | JWT secret key | `jwt_secure_secret_key_2025_generated_for_production` |
| `PUSHER_APP_SECRET` | Pusher app secret | `pusher_secure_secret_key_2025_generated` |
| `GOOGLE_API_SECRET` | Google API secret | `google_api_secret_2025_generated_secure` |
| `STRIPE_SECRET` | Stripe API secret | `stripe_api_secret_2025_generated_secure` |
| `STRIPE_WEBHOOK_SECRET` | Stripe webhook secret | `stripe_webhook_secret_2025_generated` |

### Service Configuration Variables

| Variable | Description | Example Value |
|----------|-------------|---------------|
| `PUSHER_APP_ID` | Pusher app ID | `6138611` |
| `PUSHER_APP_KEY` | Pusher app key | `jSL647jlthPQkQLyfkDTwmNzkNB5s=` |
| `GOOGLE_API_KEY` | Google API key | `506408718143-pt505a3la8pktgda0o6624m4hr0nqca7.apps.googleusercontent.com` |
| `STRIPE_ENABLED` | Stripe payment enabled | `on` |
| `STRIPE_KEY` | Stripe publishable key | `OndaGames.com` |

## Services Configuration

### Web Service

- **Name**: `lucrativabet-web`
- **Memory**: 512MB
- **CPU**: 0.5
- **Port**: 8080
- **Health Check**: `/health`

### MySQL Service

- **Name**: `lucrativabet-mysql`
- **Memory**: 256MB
- **CPU**: 0.25
- **Database**: `lucrativabet`
- **Root Password**: `railway_secure_password_2025`

### Redis Service

- **Name**: `lucrativabet-redis`
- **Memory**: 128MB
- **CPU**: 0.1
- **Password**: `railway_redis_password_2025`

### Storage Volume

- **Name**: `lucrativabet-storage`
- **Size**: 1GB
- **Mount Path**: `/app/storage/app/public`

## MCP Integration

The Railway MCP integration provides management capabilities through a REST API:

### Starting the MCP Service

```bash
# Install dependencies
npm install

# Start the service
node railway-mcp.js
```

### API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/health` | GET | Health check |
| `/api/railway/status` | GET | Get Railway status |
| `/api/railway/logs` | GET | Get Railway logs |
| `/api/railway/env` | GET | Get environment variables |
| `/api/railway/env` | POST | Set environment variable |
| `/api/railway/restart` | POST | Restart service |
| `/api/railway/deploy` | POST | Trigger deployment |
| `/api/railway/project` | GET | Get project info |
| `/api/railway/services` | GET | Get services info |
| `/api/railway/domains` | GET | Get domains info |

### Webhook Integration

The MCP service supports webhooks for Railway events:

```bash
# Example webhook payload
{
  "type": "deployment.succeeded",
  "timestamp": "2025-01-01T00:00:00Z",
  "project": "lucrativabet",
  "environment": "production"
}
```

## Monitoring and Maintenance

### Health Checks

The application includes a health check endpoint at `/health`:

```json
{
  "status": "healthy",
  "timestamp": "2025-01-01T00:00:00Z",
  "app": "LucrativaBet Casino",
  "version": "1.0.0"
}
```

### Log Management

Access logs through:

1. **Railway CLI**:
   ```bash
   railway logs
   railway logs --service lucrativabet-web
   ```

2. **MCP API**:
   ```bash
   curl http://localhost:3001/api/railway/logs
   ```

3. **Railway Dashboard**: View logs in the Railway web interface

### Database Backups

Database backups are stored in the `backups/` directory:

```bash
# List backups
ls -la backups/

# Restore from backup
mysql -h ${MYSQL.HOST} -u ${MYSQL.USERNAME} -p${MYSQL.PASSWORD} ${MYSQL.DATABASE} < backups/backup_file.sql
```

### Performance Optimization

The deployment includes several performance optimizations:

1. **Opcode Caching**: PHP OPcache enabled
2. **Configuration Caching**: `php artisan config:cache`
3. **Route Caching**: `php artisan route:cache`
4. **View Caching**: `php artisan view:cache`
5. **Redis Caching**: Redis for sessions and cache

## Troubleshooting

### Common Issues

#### 1. Application Not Starting

**Symptoms**: 502 errors, service not responding

**Solutions**:
- Check logs: `railway logs`
- Verify environment variables
- Check health endpoint: `/health`
- Restart service: `railway restart`

#### 2. Database Connection Issues

**Symptoms**: Database connection errors, migration failures

**Solutions**:
- Verify MySQL service is running
- Check database connection variables
- Test database connection:
  ```bash
  railway run mysql -h ${MYSQL.HOST} -u ${MYSQL.USERNAME} -p${MYSQL.PASSWORD} ${MYSQL.DATABASE}
  ```

#### 3. Redis Connection Issues

**Symptoms**: Cache errors, session issues

**Solutions**:
- Verify Redis service is running
- Check Redis connection variables
- Test Redis connection:
  ```bash
  railway run redis-cli -h ${REDIS.HOST} -p ${REDIS.PORT} -a ${REDIS.PASSWORD} ping
  ```

#### 4. File Permission Issues

**Symptoms**: Upload failures, storage issues

**Solutions**:
- Check storage permissions
- Ensure storage volume is mounted correctly
- Verify storage link exists: `php artisan storage:link`

### Debug Commands

#### Railway CLI Commands

```bash
# Check status
railway status

# View logs
railway logs

# View environment variables
railway variables

# Restart service
railway restart

# Open shell
railway shell
```

#### Laravel Commands

```bash
# Clear cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Run migrations
php artisan migrate

# Check storage link
php artisan storage:link
```

### Support

For additional support:

1. **Railway Documentation**: [docs.railway.app](https://docs.railway.app)
2. **Laravel Documentation**: [laravel.com/docs](https://laravel.com/docs)
3. **GitHub Issues**: Create an issue in the project repository
4. **Railway Support**: support@railway.app

## Conclusion

This guide provides all the necessary information for deploying LucrativaBet to Railway. The deployment has been optimized for performance, security, and reliability. Follow the steps carefully and refer to the troubleshooting section if you encounter any issues.

For the latest updates and additional information, please refer to the project documentation and Railway's official documentation.