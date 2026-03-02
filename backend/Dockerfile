FROM php:8.4-fpm

# Устанавливаем зависимости
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd intl zip

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Устанавливаем рабочию директорию
WORKDIR /var/www/html

# Копируем composer.json и composer.lock (если есть)
COPY composer.json composer.lock* ./

# Устанавливаем зависимости включая pusher
RUN composer install --no-scripts --no-autoloader --ignore-platform-req=php --ignore-platform-req=ext-intl

# Копируем остальной код
COPY . .

# Завершаем установку и оптимизацию
RUN composer dump-autoload --optimize --ignore-platform-req=php --ignore-platform-req=ext-intl

# Пользователь
RUN chown -R www-data:www-data /var/www/html

CMD ["php-fpm"]