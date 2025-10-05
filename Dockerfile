FROM php:8.2-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    supervisor \
    nginx \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar o trabalho directory
WORKDIR /var/www

# Copiar arquivos do projeto
COPY . /var/www
RUN chown -R www-data:www-data /var/www

# Instalar dependências do PHP
RUN composer install --no-dev --optimize-autoloader

# Copiar e configurar nginx
COPY nginx.conf /etc/nginx/sites-available/default

# Copiar arquivo de configuração do supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Configuração de permissões
RUN mkdir -p /var/www/storage/logs \
    && chmod -R 777 /var/www/storage \
    && chmod -R 777 /var/www/bootstrap/cache

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]