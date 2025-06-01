# Use official PHP 8.1 with Apache
FROM php:8.1-apache

# Install necessary system packages: ffmpeg, python3, pip, and tools
RUN apt-get update && apt-get install -y \
    ffmpeg \
    python3 \
    python3-pip \
    curl \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Install yt-dlp globally using pip3
RUN pip3 install --no-cache-dir yt-dlp

# Enable Apache rewrite module (optional, useful if you use .htaccess)
RUN a2enmod rewrite

# Copy your entire project into the Apache web root
COPY . /var/www/html/

# Set correct permissions (optional but recommended)
RUN chown -R www-data:www-data /var/www/html
