
FROM php:8.2-apache

 
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    wget \
    && rm -rf /var/lib/apt/lists/*


RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_pgsql \
    pgsql \
    gd


RUN wget https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64 \
    && chmod +x mhsendmail_linux_amd64 \
    && mv mhsendmail_linux_amd64 /usr/local/bin/mhsendmail


#COPY ./apache/apache.conf /etc/apache2/sites-available/000-default.conf


RUN a2enmod rewrite

 
 

RUN mkdir -p /var/www/html/uploads \
    && chmod -R 777 /var/www/html \
    && chmod -R 777 /var/www/html/public \
    && chmod -R 777 /var/www/html/uploads

WORKDIR /var/www/html
