FROM php:8.3-fpm-alpine

# 1) Ferramentas de build para extensões PECL (phpize, autoconf, make, g++, etc.)
#    linux-headers ajuda a evitar erros de build em Alpine
RUN apk add --no-cache $PHPIZE_DEPS linux-headers

# 2) Extensões nativas do PHP
RUN docker-php-ext-install pdo pdo_mysql

# 3) Instala e habilita Xdebug (pinado para evitar problemas de resolução de versão)
#    Dica: se falhar, troque a versão abaixo por outra estável 3.3.x
ENV XDEBUG_VERSION=3.3.2
RUN pecl install xdebug-${XDEBUG_VERSION} \
 && docker-php-ext-enable xdebug

# 4) Configurações do Xdebug (apenas cobertura)
RUN { \
  echo "xdebug.mode=coverage"; \
  echo "xdebug.start_with_request=off"; \
} > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# 5) Checagens em tempo de build — se algo falhar, o build PARA aqui
RUN set -eux; \
  php -v; \
  php -m | grep -i xdebug; \
  php --ri xdebug

# 6) Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN mkdir -p storage/logs storage/cache \
    && chown -R www-data:www-data storage
