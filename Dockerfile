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

# Criar arquivo de configuração nginx
RUN echo 'server {' > /etc/nginx/nginx.conf && \
    echo '    listen 3000;' >> /etc/nginx/nginx.conf && \
    echo '    server_name _;' >> /etc/nginx/nginx.conf && \
    echo '    root /var/www/html/public;' >> /etc/nginx/nginx.conf && \
    echo '    index index.php index.html;' >> /etc/nginx/nginx.conf && \
    echo '    location / {' >> /etc/nginx/nginx.conf && \
    echo '        try_files $uri $uri/ /index.php?$query_string;' >> /etc/nginx/nginx.conf && \
    echo '    }' >> /etc/nginx/nginx.conf && \
    echo '    location ~ \\.php$ {' >> /etc/nginx/nginx.conf && \
    echo '        fastcgi_pass unix:/var/run/php-fpm.sock;' >> /etc/nginx/nginx.conf && \
    echo '        fastcgi_index index.php;' >> /etc/nginx/nginx.conf && \
    echo '        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;' >> /etc/nginx/nginx.conf && \
    echo '        include fastcgi_params;' >> /etc/nginx/nginx.conf && \
    echo '    }' >> /etc/nginx/nginx.conf && \
    echo '    location ~ /\\.ht {' >> /etc/nginx/nginx.conf && \
    echo '        deny all;' >> /etc/nginx/nginx.conf && \
    echo '    }' >> /etc/nginx/nginx.conf && \
    echo '}' >> /etc/nginx/nginx.conf

# Criar configuração do PHP-FPM
RUN echo '[www]' > /etc/php8/php-fpm.conf && \
    echo 'listen = /var/run/php-fpm.sock' >> /etc/php8/php-fpm.conf && \
    echo 'listen.owner = www-data' >> /etc/php8/php-fpm.conf && \
    echo 'listen.group = www-data' >> /etc/php8/php-fpm.conf && \
    echo 'user = www-data' >> /etc/php8/php-fpm.conf && \
    echo 'group = www-data' >> /etc/php8/php-fpm.conf && \
    echo 'pm = dynamic' >> /etc/php8/php-fpm.conf && \
    echo 'pm.max_children = 50' >> /etc/php8/php-fpm.conf && \
    echo 'pm.start_servers = 2' >> /etc/php8/php-fpm.conf && \
    echo 'pm.min_spare_servers = 1' >> /etc/php8/php-fpm.conf && \
    echo 'pm.max_spare_servers = 3' >> /etc/php8/php-fpm.conf && \
    echo 'pm.max_requests = 500' >> /etc/php8/php-fpm.conf && \
    echo 'catch_workers_output = yes' >> /etc/php8/php-fpm.conf

# Criar configuração do supervisor
RUN echo '[supervisord]' > /etc/supervisor/conf.d/supervisord.conf && \
    echo 'nodaemon=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'user=root' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '[program:nginx]' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'command=nginx -g "daemon off;"' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'autostart=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'autorestart=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stdout_logfile=/dev/stdout' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stdout_logfile_maxbytes=0' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stderr_logfile=/dev/stderr' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stderr_logfile_maxbytes=0' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '[program:php-fpm]' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'command=php-fpm8 -F' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'autostart=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'autorestart=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stdout_logfile=/dev/stdout' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stdout_logfile_maxbytes=0' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stderr_logfile=/dev/stderr' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stderr_logfile_maxbytes=0' >> /etc/supervisor/conf.d/supervisord.conf

# Criar script de inicialização
RUN echo '#!/bin/sh' > /start.sh && \
    echo 'set -e' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Criar .env com variáveis do Railway' >> /start.sh && \
    echo 'echo "APP_NAME=\"Lucrativa Bet\"" > .env' >> /start.sh && \
    echo 'echo "APP_ENV=production" >> .env' >> /start.sh && \
    echo 'echo "APP_KEY=base64:jP1f2K0S7Xe5JkyxyP8EptDNe8w77mGYOWcEoZyH9FU=" >> .env' >> /start.sh && \
    echo 'echo "APP_DEBUG=false" >> .env' >> /start.sh && \
    echo 'echo "APP_DEMO=false" >> .env' >> /start.sh && \
    echo 'echo "APP_URL=${RAILWAY_PUBLIC_DOMAIN:-https://lucrativabet.onrailway.app}" >> .env' >> /start.sh && \
    echo 'echo "VITE_BASE_URL=/" >> .env' >> /start.sh && \
    echo 'echo "FILAMENT_BASE_URL=admin" >> .env' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Configuração do banco de dados Railway' >> /start.sh && \
    echo 'echo "DB_CONNECTION=pgsql" >> .env' >> /start.sh && \
    echo 'echo "DB_HOST=${RAILWAY_PRIVATE_DOMAIN}" >> .start.sh && \
    echo 'echo "DB_PORT=5432" >> .env' >> /start.sh && \
    echo 'echo "DB_DATABASE=${PGDATABASE}" >> .env' >> /start.sh && \
    echo 'echo "DB_USERNAME=${PGUSER}" >> .env' >> /start.sh && \
    echo 'echo "DB_PASSWORD=${PGPASSWORD}" >> .env' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Configurações adicionais' >> /start.sh && \
    echo 'echo "LOG_CHANNEL=stack" >> .env' >> /start.sh && \
    echo 'echo "LOG_LEVEL=error" >> .env' >> /start.sh && \
    echo 'echo "CACHE_DRIVER=file" >> .env' >> /start.sh && \
    echo 'echo "SESSION_DRIVER=file" >> .env' >> /start.sh && \
    echo 'echo "QUEUE_CONNECTION=database" >> .env' >> /start.sh && \
    echo 'echo "FILESYSTEM_DISK=local" >> .env' >> /start.sh && \
    echo 'echo "FORCE_HTTPS=true" >> .env' >> /start.sh && \
    echo 'echo "TRUSTED_PROXIES=*" >> .env' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Configurações do PlayFiver' >> /start.sh && \
    echo 'echo "PLAYFIVER_URL=https://api.playfivers.com" >> .env' >> /start.sh && \
    echo 'echo "PLAYFIVER_CODE=sorte365bet" >> .env' >> /start.sh && \
    echo 'echo "PLAYFIVER_TOKEN=a9aa0e61-9179-466a-8d7b-e22e7b473b8a" >> .env' >> /start.sh && \
    echo 'echo "PLAYFIVER_SECRET=f41adb6a-e15b-46b4-ad5a-1fc49f4745df" >> .env' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Configurações de backup' >> /start.sh && \
    echo 'echo "PLAYFIVER_BACKUP_CODE=lucrativabt" >> .env' >> /start.sh && \
    echo 'echo "PLAYFIVER_BACKUP_TOKEN=80609b36-a25c-4175-92c5-c9a6f1e1b06e" >> .env' >> /start.sh && \
    echo 'echo "PLAYFIVER_BACKUP_SECRET=08cfba85-7652-4a00-903f-7ea649620eb2" >> .env' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Configurações Filament' >> /start.sh && \
    echo 'echo "FILAMENT_DATABASE_NOTIFICATIONS_ENABLED=true" >> .env' >> /start.sh && \
    echo 'echo "FILAMENT_BROADCASTING_ECHO=false" >> .env' >> /start.sh && \
    echo 'echo "FILAMENT_BROADCASTING_REVERB=false" >> .env' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Aguardar banco de dados' >> /start.sh && \
    echo 'if [ "$PGHOST" ]; then' >> /start.sh && \
    echo '  echo "Aguardando banco de dados em $PGHOST:5432..."' >> /start.sh && \
    echo '  sleep 10' >> /start.sh && \
    echo 'fi' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Executar migrações' >> /start.sh && \
    echo 'php artisan migrate --force' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Otimizar aplicação' >> /start.sh && \
    echo 'php artisan config:cache' >> /start.sh && \
    echo 'php artisan route:cache' >> /start.sh && \
    echo 'php artisan view:cache' >> /start.sh && \
    echo 'php artisan event:cache' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Criar link simbólico para storage' >> /start.sh && \
    echo 'php artisan storage:link' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Iniciar supervisor' >> /start.sh && \
    echo '/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf' >> /start.sh && \
    chmod +x /start.sh

# Porta para o Railway
EXPOSE 3000

# Comando de entrada
CMD ["/start.sh"]