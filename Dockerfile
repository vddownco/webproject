# Use official PHP with Apache
FROM php:8.1-apache

# Install system packages needed for yt-dlp and ffmpeg
RUN apt-get update && apt-get install -y \
    ffmpeg \
    python3 \
    python3-pip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install yt-dlp globally
RUN pip3 install yt-dlp

# Copy your project files to Apache web root
COPY . /var/www/html/

# Enable Apache mod_rewrite (optional, for cleaner URLs)
RUN a2enmod rewrite
