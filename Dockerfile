FROM php:8.5-fpm

# ដំឡើង System dependencies និង PHP Extensions ចាំបាច់សម្រាប់ PostgreSQL និង Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (รวมถึง pdo_pgsql)
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# យក Composer មកប្រើប្រាស់
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-interaction --optimize-autoloader

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# កំណត់ Port សម្រាប់ Render (Render ជាទូទៅប្រើប្រាស់ Port 10000 ស្រាប់)
ENV PORT=10000
EXPOSE 10000

# ប្រើប្រាស់ PHP Built-in Server ជំនួសឱ្យ php-fpm ដើម្បីឱ្យ Render អាចចាប់យក HTTP Port បាន
CMD php artisan serve --host=0.0.0.0 --port=$PORT