# এখানে PHP 8.4 ব্যবহার করা হলো
FROM php:8.4-apache

# Install dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Enable Apache mod_rewrite for Laravel routing
RUN a2enmod rewrite
RUN { \
    echo 'upload_max_filesize = 10M'; \
    echo 'post_max_size = 12M'; \
    } > /usr/local/etc/php/conf.d/uploads.ini
# Change Apache DocumentRoot to Laravel's public folder
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf


# Copy all project files
COPY . /var/www/html/

# Install Composer and project dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader

# Set perfect permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# --- অটোমেটিক মাইগ্রেশনের জন্য এন্ট্রি পয়েন্ট সেটআপ ---
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]

# Open port 80 for Render
EXPOSE 80