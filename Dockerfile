# Build stage para Composer
FROM php:8.2-fpm-alpine AS composer-build
WORKDIR /app

# Instalar dependências necessárias para todas as extensões PHP
RUN apk update && apk add --no-cache \
    icu-dev \
    icu-libs \
    oniguruma-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    postgresql-dev

# Instalar todas as extensões PHP necessárias
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip opcache intl

# Instalar Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Instalar dependências do composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Build stage para Node
FROM node:18-alpine AS node-build
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# Production stage
FROM php:8.2-fpm-alpine

# Instalar dependências do sistema
RUN apk update && apk add --no-cache \
    nginx \
    supervisor \
    curl \
    zip \
    unzip \
    git \
    oniguruma-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    postgresql-dev \
    mysql-client \
    postgresql-client \
    icu-dev \
    icu-libs

# Instalar extensões PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip opcache intl

# Configurar PHP
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos do projeto
COPY . .

# Copiar vendor do build stage
COPY --from=composer-build /app/vendor ./vendor

# Copiar assets compilados do build stage
COPY --from=node-build /app/public/build ./public/build
COPY --from=node-build /app/node_modules ./node_modules

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copiar configurações
COPY nginx-render.conf /etc/nginx/nginx.conf
COPY supervisord-render.conf /etc/supervisor/conf.d/supervisord.conf

# Criar script de inicialização
RUN echo '#!/bin/sh' > /start.sh && \
    echo 'set -e' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Aguardar banco de dados' >> /start.sh && \
    echo 'if [ "$DATABASE_URL" ]; then' >> /start.sh && \
    echo '  echo "Aguardando banco de dados..."' >> /start.sh && \
    echo '  sleep 10' >> /start.sh && \
    echo 'fi' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Executar migrações (apenas se o banco estiver disponível)' >> /start.sh && \
    echo 'php artisan migrate --force || true' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Otimizar aplicação' >> /start.sh && \
    echo 'php artisan config:cache' >> /start.sh && \
    echo 'php artisan route:cache' >> /start.sh && \
    echo 'php artisan view:cache' >> /start.sh && \
    echo 'php artisan event:cache' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Criar link simbólico para storage' >> /start.sh && \
    echo 'php artisan storage:link || true' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Iniciar supervisor' >> /start.sh && \
    echo '/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf' >> /start.sh && \
    chmod +x /start.sh

# Porta para o Render
EXPOSE 10000

# Comando de entrada
CMD ["/start.sh"]