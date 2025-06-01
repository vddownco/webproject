FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    python3 python3-pip ffmpeg curl unzip git build-essential python3-dev libffi-dev libssl-dev \
    && rm -rf /var/lib/apt/lists/*

# Suppress pip root warning
ENV PIP_ROOT_USER_ACTION=ignore

# Upgrade pip and install yt-dlp
RUN pip3 install --upgrade pip setuptools wheel --break-system-packages
RUN pip3 install --no-cache-dir yt-dlp --break-system-packages

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy project files to web root
COPY . /var/www/html/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
