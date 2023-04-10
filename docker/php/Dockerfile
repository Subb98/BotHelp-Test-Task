FROM php:8.2.4-cli-alpine3.17

ENV ROOT_DIR="/usr/local/src"
ENV APP_ENV="development"
ENV USER=docker
ENV UID=1000

WORKDIR "/usr/local/src"

EXPOSE 8000

CMD ["/usr/local/bin/php", "/usr/local/src/bin/server", "start"]

RUN apk add --no-cache \
    linux-headers

RUN docker-php-ext-install \
    pcntl \
    sockets

RUN curl https://getcomposer.org/composer.phar > /usr/local/bin/composer \
    && chmod a+x /usr/local/bin/composer

RUN adduser \
    --disabled-password \
    --gecos "" \
    --no-create-home \
    --uid "$UID" \
    "$USER"

USER "$USER"
