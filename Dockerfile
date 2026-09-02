FROM php:8.2-apache

# ─── Metadata ─────────────────────────────────────────────────────────────────
LABEL maintainer="StyloKonfort Dev Team"
LABEL description="StyloKonfort Ecommerce — PHP 8.2 + Apache"

# ─── Habilitar módulos Apache ─────────────────────────────────────────────────
RUN a2enmod rewrite headers

# ─── Instalar dependencias del sistema + extensiones PHP ─────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libwebp-dev \
        libonig-dev \
        libicu-dev \
        zip \
        unzip \
        curl \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        intl \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ─── Configurar OPcache para producción ──────────────────────────────────────
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=60'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=0'; \
} > /usr/local/etc/php/conf.d/opcache.ini

# ─── Configurar PHP para producción ──────────────────────────────────────────
RUN { \
    echo 'upload_max_filesize=10M'; \
    echo 'post_max_size=12M'; \
    echo 'memory_limit=256M'; \
    echo 'max_execution_time=60'; \
    echo 'expose_php=Off'; \
    echo 'display_errors=Off'; \
    echo 'log_errors=On'; \
    echo 'error_log=/var/log/apache2/php_errors.log'; \
} > /usr/local/etc/php/conf.d/stylokonfort.ini

# ─── Instalar Composer ────────────────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ─── Directorio de trabajo ────────────────────────────────────────────────────
WORKDIR /var/www/html

# ─── Copiar el proyecto ───────────────────────────────────────────────────────
COPY . /var/www/html/

# ─── Instalar dependencias PHP ────────────────────────────────────────────────
RUN composer install --no-dev --optimize-autoloader --no-interaction 2>&1

# ─── Crear directorios de uploads con permisos correctos ─────────────────────
RUN mkdir -p \
        assets/images/productos \
        assets/images/clientes \
        assets/images/categorias \
        assets/images/marcas \
        assets/images/sliders \
        assets/images/carrusel \
        assets/images/promociones \
        assets/images/perfil \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/assets/images

# ─── Configurar VirtualHost de Apache ────────────────────────────────────────
RUN { \
    echo '<VirtualHost *:80>'; \
    echo '    DocumentRoot /var/www/html'; \
    echo '    <Directory /var/www/html>'; \
    echo '        Options -Indexes +FollowSymLinks'; \
    echo '        AllowOverride All'; \
    echo '        Require all granted'; \
    echo '    </Directory>'; \
    echo '    ErrorLog ${APACHE_LOG_DIR}/error.log'; \
    echo '    CustomLog ${APACHE_LOG_DIR}/access.log combined'; \
    echo '</VirtualHost>'; \
} > /etc/apache2/sites-available/000-default.conf

# ─── Health Check ─────────────────────────────────────────────────────────────
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

EXPOSE 80
