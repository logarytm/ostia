FROM php:7.4-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    locales \
    vim \
    ffmpeg \
    nodejs \
    npm \
    unzip && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql exif pcntl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN groupadd -g 1000 app
RUN useradd -u 1000 -ms /bin/bash -g app app

COPY . /var/www
COPY --chown=app:app . /var/www

USER app

EXPOSE 9000
CMD ["php-fpm"]
