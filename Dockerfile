FROM php:8.2-cli as web

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:2.4.4 /usr/bin/composer /usr/local/bin/composer

# Add a new user "john" with user id 8877
RUN useradd -u 8877 john

# Set working directory
WORKDIR /app
COPY . . 

RUN chmod -R 775 /app
RUN chown -R john:john /app

# Change to non-root privilege
USER john

RUN composer install --no-scripts --no-plugins