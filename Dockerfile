FROM php:8.0.0-apache
ARG DEBIAN_FRONTEND=noninteractive

# Actualizar paquetes y dependencias necesarias antes de instalar extensiones PHP
RUN apt-get update && apt-get install -y \
    sendmail \
    libpng-dev \
    libzip-dev \
    zlib1g-dev \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP necesarias
RUN docker-php-ext-install mysqli mbstring zip gd

# Habilitar el módulo rewrite en Apache
RUN a2enmod rewrite
