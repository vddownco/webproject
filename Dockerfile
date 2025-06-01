FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    ffmpeg python3 python3-pip curl unzip git build-essential python3-dev libffi-dev libssl-dev \
    && rm -rf /var/lib/apt/lists/*

RUN pip3 install --upgrade pip setuptools wheel
RUN pip3 install --no-cache-dir yt-dlp

RUN a2enmod rewrite

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html
