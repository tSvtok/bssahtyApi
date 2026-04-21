FROM php:8.3-fpm

# 1. Définir le dossier de travail dès le début
WORKDIR /var/www

# 2. Installation des dépendances système (Optimisée)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    locales \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Installation des extensions PHP
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl

# 4. Récupérer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Gestion de l'utilisateur système (waae)
# On s'assure que l'UID 1000 correspond à ton utilisateur Linux/Mac local
RUN useradd -G www-data,root -u 1000 -d /home/waae waae
RUN mkdir -p /home/waae/.composer && chown -R waae:waae /home/waae

# 6. Copie des fichiers et gestion des permissions
# On copie d'abord le code, puis on change le propriétaire
COPY . /var/www
RUN chown -R waae:www-data /var/www

# 7. Passer à l'utilisateur non-root
USER waae

EXPOSE 9000

CMD ["php-fpm"]