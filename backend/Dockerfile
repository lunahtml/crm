FROM php:8.4-fpm

# Устанавливаем зависимости
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo_pgsql pgsql exif pcntl bcmath gd intl zip

# Отдельно устанавливаем oniguruma и mbstring
RUN apt-get install -y libonig-dev \
    && docker-php-ext-install mbstring

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Копируем composer.json (ВАЖНО: сначала composer, потом все остальное)
COPY composer.json composer.lock* ./

# Устанавливаем PHP зависимости (vendor)
RUN composer install --no-scripts --no-autoloader --ignore-platform-req=php --ignore-platform-req=ext-intl

# Копируем package.json
COPY package*.json ./
RUN npm install

# Копируем остальной код
COPY . .

# Собираем фронтенд
RUN npm run build

# Генерируем автозагрузку
RUN composer dump-autoload --optimize --ignore-platform-req=php --ignore-platform-req=ext-intl

# Права
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 storage bootstrap/cache

CMD ["php-fpm"]