# Sử dụng PHP 8.1 với Apache
FROM php:8.1-apache

# Cài đặt các extension cần thiết
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    && docker-php-ext-install gd pdo pdo_mysql mbstring

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Sao chép mã nguồn vào container
WORKDIR /var/www/html
COPY . .

# Cài đặt dependencies
RUN composer install --no-dev --optimize-autoloader

# Chạy Laravel migrations
RUN php artisan migrate --force

# Phân quyền thư mục storage và cache
RUN chmod -R 775 storage bootstrap/cache

# Expose port 80
EXPOSE 80

# Khởi động Apache
CMD ["apache2-foreground"]
