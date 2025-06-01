FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    ffmpeg \
    python3 \
    python3-pip \
    curl \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Upgrade pip and install setuptools & wheel before installing yt-dlp
RUN pip3 install --upgrade pip setuptools wheel && \
    pip3 install yt-dlp

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy your PHP files into Apache root
COPY . /var/www/html/

# Set file permissions (optional)
RUN chown -R www-data:www-data /var/www/html
