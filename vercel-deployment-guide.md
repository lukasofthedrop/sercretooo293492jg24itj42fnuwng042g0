# API DEPLOYMENT GUIDE - LARAVEL ON VERCEL

## Overview
This guide explains how to deploy Laravel applications on Vercel platform.

## Prerequisites
- Vercel account
- PostgreSQL database (Vercel or external)
- Redis for caching (Upstash Redis recommended)
- Laravel application optimized for serverless

## Environment Variables
Set these in Vercel dashboard:

### Core Laravel
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY=your_base64_encoded_key`
- `APP_URL=${VERCEL_URL}`

### Database (Vercel Postgres)
- `DB_CONNECTION=pgsql`
- `DB_HOST=${POSTGRES_HOST}`
- `DB_PORT=${POSTGRES_PORT}`
- `DB_DATABASE=${POSTGRES_DATABASE}`
- `DB_USERNAME=${POSTGRES_USER}`
- `DB_PASSWORD=${POSTGRES_PASSWORD}`

### Cache (Upstash Redis)
- `CACHE_DRIVER=redis`
- `REDIS_HOST=${REDIS_HOST}`
- `REDIS_PASSWORD=${REDIS_PASSWORD}`
- `REDIS_PORT=${REDIS_PORT}`

## Deployment Commands
```bash
# Install dependencies
npm install

# Build frontend assets
npm run build

# Deploy to Vercel
vercel --prod
```

## Important Notes
- Use serverless-compatible Laravel configurations
- Optimize for cold starts
- Implement proper caching strategies
- Configure CDN for static assets
- Use Vercel Cron Jobs for scheduled tasks