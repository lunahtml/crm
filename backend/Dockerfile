FROM php:8.4-fpm

# Устанавливаем зависимости (разделим на части для надежности)
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

# Копируем package.json
COPY package*.json ./
RUN npm install

# Копируем остальной код
COPY . .

# Собираем фронтенд
RUN npm run build

# Копируем composer.json
COPY composer.json composer.lock* ./
RUN composer install --no-scripts --no-autoloader --ignore-platform-req=php --ignore-platform-req=ext-intl
RUN composer dump-autoload --optimize --ignore-platform-req=php --ignore-platform-req=ext-intl

RUN chown -R www-data:www-data /var/www/html

CMD ["php-fpm"]