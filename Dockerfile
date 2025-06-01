# Use official PHP 8.1 with Apache base image
FROM php:8.1-apache

# Install system dependencies including python3, pip, ffmpeg, and other needed libs
RUN apt-get update && apt-get install -y \
    python3 python3-pip ffmpeg curl unzip git build-essential python3-dev libffi-dev libssl-dev \
    && rm -rf /var/lib/apt/lists/*

# Upgrade pip and install yt-dlp with the break-system-packages flag to avoid errors
RUN pip3 install --upgrade pip setuptools wheel --break-system-packages
RUN pip3 install --no-cache-dir yt-dlp --break-system-packages

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy your PHP app code into the Apache web root
COPY . /var/www/html/

# Fix permissions so Apache can serve the files properly
RUN chown -R www-data:www-data /var/www/html

# Expose HTTP port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
