FROM php:8.1-fpm

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update \
    && apt-get install -y zlib1g-dev \
                git \
                zip \
                unzip \
                libonig-dev \
                libfreetype6-dev \ 
                libjpeg62-turbo-dev \
                libpng-dev \
                libjpeg-dev \
    && apt-get install -y libzip-dev \
    && docker-php-ext-install zip \
    && docker-php-ext-install \ 
                    bcmath \
                    gd \
    && docker-php-ext-configure gd \
                --with-freetype=/usr/include/ \
                --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

# RUN curl -sL https://deb.nodesource.com/setup_10.x | bash -
# RUN apt-get install -y nodejs
# RUN apt-get install -y nodejs npm

# ENV NODE_VERSION=16.14.0
# RUN curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.34.0/install.sh | bash
# ENV NVM_DIR=/root/.nvm
# RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION}
# RUN . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION}
# RUN . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
# ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"
# RUN node --version
# RUN npm --version




# Laravel
# WORKDIR /var/www/html/nx-careerroad-rakusai
# ADD . /var/www/html/nx-careerroad-rakusai/storage
# RUN chown -R www-data:www-data /var/www/html/nx-careerroad-rakusai/storage
