FROM php:8.1-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    libxml2-dev \
    libsoap-dev \
    git \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql intl zip xmlrpc soap opcache

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Set recommended PHP.ini settings
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=60'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
} > /usr/local/etc/php/conf.d/opcache-recommended.ini

RUN { \
    echo 'upload_max_filesize = 512M'; \
    echo 'post_max_size = 512M'; \
    echo 'max_execution_time = 600'; \
    echo 'max_input_vars = 5000'; \
} > /usr/local/etc/php/conf.d/uploads.ini

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Change Apache DocumentRoot to point to /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy application source
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html
