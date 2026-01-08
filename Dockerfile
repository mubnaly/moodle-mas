# ==============================================================================
# Moodle Backend Production Dockerfile
# Optimized for Coolify/VPS Deployment
# ==============================================================================
FROM php:8.1-apache

LABEL maintainer="Your Brand Team"
LABEL description="Moodle LMS Backend - Production Ready"
LABEL version="1.0.0"

# ==============================================================================
# Environment Variables for Configuration
# ==============================================================================
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
ENV MOODLEDATA_PATH=/var/www/moodledata
ENV PHP_MEMORY_LIMIT=512M
ENV PHP_MAX_EXECUTION_TIME=300
ENV PHP_MAX_INPUT_VARS=5000
ENV PHP_UPLOAD_MAX_FILESIZE=512M
ENV PHP_POST_MAX_SIZE=512M
ENV OPCACHE_ENABLE=1
ENV OPCACHE_MEMORY_CONSUMPTION=256
ENV OPCACHE_MAX_FILES=10000
ENV OPCACHE_REVALIDATE_FREQ=0

# ==============================================================================
# Install System Dependencies
# ==============================================================================
RUN apt-get update && apt-get install -y --no-install-recommends \
    # Image processing
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    # Database
    libpq-dev \
    # Internationalization
    libicu-dev \
    # Archive handling
    libzip-dev \
    # XML processing
    libxml2-dev \
    # LDAP (optional, for enterprise auth)
    libldap2-dev \
    # SSL/TLS
    libssl-dev \
    # Tools
    git \
    unzip \
    curl \
    cron \
    supervisor \
    # Clean up
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ==============================================================================
# Configure and Install PHP Extensions
# ==============================================================================
# Configure GD extension
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp

# Configure LDAP extension
RUN docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/

# Install PHP extensions required by Moodle
RUN docker-php-ext-install -j$(nproc) \
    gd \
    pdo \
    pdo_pgsql \
    pgsql \
    intl \
    zip \
    soap \
    opcache \
    ldap \
    exif \
    pcntl \
    bcmath

# Install Redis extension for session/cache handling
RUN pecl install redis-6.0.2 \
    && docker-php-ext-enable redis

# Install APCu for user cache
RUN pecl install apcu-5.1.23 \
    && docker-php-ext-enable apcu

# ==============================================================================
# PHP Configuration for Production
# ==============================================================================
RUN { \
    echo '[opcache]'; \
    echo 'opcache.enable=${OPCACHE_ENABLE}'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.memory_consumption=${OPCACHE_MEMORY_CONSUMPTION}'; \
    echo 'opcache.interned_strings_buffer=32'; \
    echo 'opcache.max_accelerated_files=${OPCACHE_MAX_FILES}'; \
    echo 'opcache.revalidate_freq=${OPCACHE_REVALIDATE_FREQ}'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.save_comments=1'; \
    echo 'opcache.huge_code_pages=0'; \
    } > /usr/local/etc/php/conf.d/opcache-production.ini

RUN { \
    echo '[PHP]'; \
    echo 'memory_limit=${PHP_MEMORY_LIMIT}'; \
    echo 'max_execution_time=${PHP_MAX_EXECUTION_TIME}'; \
    echo 'max_input_vars=${PHP_MAX_INPUT_VARS}'; \
    echo 'upload_max_filesize=${PHP_UPLOAD_MAX_FILESIZE}'; \
    echo 'post_max_size=${PHP_POST_MAX_SIZE}'; \
    echo 'display_errors=Off'; \
    echo 'log_errors=On'; \
    echo 'error_log=/var/log/php_errors.log'; \
    echo 'date.timezone=UTC'; \
    echo 'session.cookie_httponly=1'; \
    echo 'session.cookie_secure=1'; \
    echo 'session.use_strict_mode=1'; \
    echo 'expose_php=Off'; \
    echo 'allow_url_fopen=On'; \
    echo 'default_socket_timeout=60'; \
    echo 'realpath_cache_size=4096K'; \
    echo 'realpath_cache_ttl=600'; \
    } > /usr/local/etc/php/conf.d/production.ini

RUN { \
    echo '[apcu]'; \
    echo 'apc.enabled=1'; \
    echo 'apc.shm_size=256M'; \
    echo 'apc.ttl=7200'; \
    echo 'apc.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/apcu.ini

# ==============================================================================
# Apache Configuration
# ==============================================================================
# Enable required Apache modules
RUN a2enmod rewrite headers expires deflate ssl

# Configure Apache for security and performance
RUN { \
    echo 'ServerTokens Prod'; \
    echo 'ServerSignature Off'; \
    echo 'TraceEnable Off'; \
    echo 'FileETag None'; \
    echo 'Header always set X-Content-Type-Options "nosniff"'; \
    echo 'Header always set X-Frame-Options "SAMEORIGIN"'; \
    echo 'Header always set X-XSS-Protection "1; mode=block"'; \
    echo 'Header always set Referrer-Policy "strict-origin-when-cross-origin"'; \
    } > /etc/apache2/conf-available/security-headers.conf \
    && a2enconf security-headers

# Configure Apache document root
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configure Apache performance
RUN { \
    echo '<IfModule mod_deflate.c>'; \
    echo '  AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json'; \
    echo '</IfModule>'; \
    echo '<IfModule mod_expires.c>'; \
    echo '  ExpiresActive On'; \
    echo '  ExpiresByType image/png "access plus 1 month"'; \
    echo '  ExpiresByType image/gif "access plus 1 month"'; \
    echo '  ExpiresByType image/jpg "access plus 1 month"'; \
    echo '  ExpiresByType image/jpeg "access plus 1 month"'; \
    echo '  ExpiresByType image/svg+xml "access plus 1 month"'; \
    echo '  ExpiresByType text/css "access plus 1 week"'; \
    echo '  ExpiresByType application/javascript "access plus 1 week"'; \
    echo '</IfModule>'; \
    } > /etc/apache2/conf-available/performance.conf \
    && a2enconf performance

# ==============================================================================
# Create Moodle Data Directory
# ==============================================================================
RUN mkdir -p ${MOODLEDATA_PATH} \
    && mkdir -p /var/log/moodle

# ==============================================================================
# Cron Configuration for Moodle
# ==============================================================================
RUN { \
    echo '# Moodle cron job - runs every minute'; \
    echo '* * * * * www-data /usr/local/bin/php /var/www/html/public/admin/cli/cron.php >> /var/log/moodle/cron.log 2>&1'; \
    } > /etc/cron.d/moodle-cron \
    && chmod 0644 /etc/cron.d/moodle-cron

# ==============================================================================
# Supervisor Configuration
# ==============================================================================
RUN { \
    echo '[supervisord]'; \
    echo 'nodaemon=true'; \
    echo 'logfile=/var/log/supervisor/supervisord.log'; \
    echo 'pidfile=/var/run/supervisord.pid'; \
    echo ''; \
    echo '[program:apache2]'; \
    echo 'command=/usr/sbin/apache2ctl -D FOREGROUND'; \
    echo 'autostart=true'; \
    echo 'autorestart=true'; \
    echo 'stdout_logfile=/var/log/apache2/access.log'; \
    echo 'stderr_logfile=/var/log/apache2/error.log'; \
    echo ''; \
    echo '[program:cron]'; \
    echo 'command=/usr/sbin/cron -f'; \
    echo 'autostart=true'; \
    echo 'autorestart=true'; \
    echo 'stdout_logfile=/var/log/cron.log'; \
    echo 'stderr_logfile=/var/log/cron_error.log'; \
    } > /etc/supervisor/conf.d/supervisord.conf

RUN mkdir -p /var/log/supervisor

# ==============================================================================
# Copy Application Source
# ==============================================================================
COPY . /var/www/html

# ==============================================================================
# Copy Entrypoint Script
# ==============================================================================
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# ==============================================================================
# Set Permissions
# ==============================================================================
RUN chown -R www-data:www-data /var/www/html \
    && chown -R www-data:www-data ${MOODLEDATA_PATH} \
    && chown -R www-data:www-data /var/log/moodle \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 ${MOODLEDATA_PATH}

# ==============================================================================
# Working Directory
# ==============================================================================
WORKDIR /var/www/html

# ==============================================================================
# Expose Ports
# ==============================================================================
EXPOSE 80

# ==============================================================================
# Health Check
# ==============================================================================
HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost/healthcheck.php || exit 1

# ==============================================================================
# Start Application
# ==============================================================================
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
