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
    nodejs \
    npm \
    && docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd intl zip

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# Копируем package.json и package-lock.json (если есть)
COPY package*.json ./

# Устанавливаем npm зависимости
RUN npm install

# Копируем остальной код
COPY . .

# Собираем фронтенд
RUN npm run build

# Копируем composer.json
COPY composer.json composer.lock* ./

# Устанавливаем PHP зависимости
RUN composer install --no-scripts --no-autoloader --ignore-platform-req=php --ignore-platform-req=ext-intl
RUN composer dump-autoload --optimize --ignore-platform-req=php --ignore-platform-req=ext-intl

# Права
RUN chown -R www-data:www-data /var/www/html

CMD ["php-fpm"]