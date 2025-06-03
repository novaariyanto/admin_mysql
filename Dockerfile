# Gunakan image PHP + Apache
FROM php:8.2-apache

# Copy semua file ke dalam direktori web
COPY . /var/www/html/

# Beri izin agar file bisa dibaca web server
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Aktifkan ekstensi mysqli dan pdo_mysql jika dibutuhkan
RUN docker-php-ext-install pdo pdo_mysql mysqli
