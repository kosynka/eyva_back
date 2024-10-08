FROM php:8.2-fpm

# set main params
ARG BUILD_ARGUMENT_DEBUG_ENABLED=false
ENV DEBUG_ENABLED=$BUILD_ARGUMENT_DEBUG_ENABLED
ARG BUILD_ARGUMENT_ENV=dev
ENV ENV=$BUILD_ARGUMENT_ENV
ENV APP_HOME /var/www
ARG UID=1000
ARG GID=1000
ENV USERNAME=www-data


# check environment
RUN if [ "$BUILD_ARGUMENT_ENV" = "default" ]; then echo "Set BUILD_ARGUMENT_ENV in docker build-args like --build-arg BUILD_ARGUMENT_ENV=dev" && exit 2; \
    elif [ "$BUILD_ARGUMENT_ENV" = "dev" ]; then echo "Building development environment."; \
    elif [ "$BUILD_ARGUMENT_ENV" = "test" ]; then echo "Building test environment."; \
    elif [ "$BUILD_ARGUMENT_ENV" = "staging" ]; then echo "Building staging environment."; \
    elif [ "$BUILD_ARGUMENT_ENV" = "prod" ]; then echo "Building production environment."; \
    else echo "Set correct BUILD_ARGUMENT_ENV in docker build-args like --build-arg BUILD_ARGUMENT_ENV=dev. Available choices are dev,test,staging,prod." && exit 2; \
    fi

# install all the dependencies and enable PHP modules
RUN apt-get update && apt-get upgrade -y && apt-get install -y \
      procps \
      nano \
      git \
      curl \
      unzip \
      libicu-dev \
      zlib1g-dev \
      libxml2 \
      libxml2-dev \
      libreadline-dev \
      supervisor \
      cron \
      sudo \
      libzip-dev \
      libpq-dev \
      libpng-dev \
      telnet \
      net-tools \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure pdo_pgsql \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
      pdo_mysql \
      pdo_pgsql \
      pgsql \
      sockets \
      intl \
      opcache \
      zip \
      gd

# Install redis
RUN pecl install redis \
    && docker-php-ext-enable redis

# Clean up temp files
RUN rm -rf /tmp/* \
        && rm -rf /var/list/apt/* \
        && rm -rf /var/lib/apt/lists/* \
        && apt-get clean

# create document root, fix permissions for www-data user and change owner to www-data
RUN mkdir -p $APP_HOME/public && \
    mkdir -p /home/$USERNAME && chown $USERNAME:$USERNAME /home/$USERNAME \
    && usermod -u $UID $USERNAME -d /home/$USERNAME \
    && groupmod -g $GID $USERNAME \
    && chown -R ${USERNAME}:${USERNAME} $APP_HOME

# put php config for Laravel
COPY ./docker/$BUILD_ARGUMENT_ENV/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./docker/$BUILD_ARGUMENT_ENV/php.ini /usr/local/etc/php/php.ini

# install Xdebug in case dev/test environment
#COPY ./docker/general/do_we_need_xdebug.sh /tmp/
#COPY ./docker/dev/xdebug.ini /tmp/
#RUN chmod u+x /tmp/do_we_need_xdebug.sh && /tmp/do_we_need_xdebug.sh

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN chmod +x /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

# add supervisor
RUN mkdir -p /var/log/supervisor
COPY --chown=root:root ./docker/general/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY --chown=root:crontab ./docker/general/cron /var/spool/cron/crontabs/root
RUN chmod 0600 /var/spool/cron/crontabs/root

# set working directory
WORKDIR $APP_HOME

USER ${USERNAME}

# copy source files and config file
#COPY --chown=${USERNAME}:${USERNAME} ../../. $APP_HOME/
#COPY --chown=${USERNAME}:${USERNAME} ../../.env.$ENV $APP_HOME/.env

# install all PHP dependencies
#RUN if [ "$BUILD_ARGUMENT_ENV" = "dev" ] || [ "$BUILD_ARGUMENT_ENV" = "test" ]; then COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --no-interaction --no-progress; \
#    else COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --no-interaction --no-progress --no-dev; \
#    fi

#RUN composer install
#
## prepare postStart scripts
#RUN chmod +x $§APP_HOME/.config/postStart.sh # &&\
RUN mkdir -p $APP_HOME/storage/logs &&\
    touch $APP_HOME/storage/logs/laravel.log &&\
    touch $APP_HOME/storage/logs/supervisor_log.log &&\
    chmod ga+wx -R $APP_HOME/storage/logs/ &&\
    chmod ga+w -R $APP_HOME/storage/ &&\
    touch $APP_HOME/storage/logs/laravel.log

### prepare php
#RUN php $APP_HOME/artisan config:cache > /dev/stdout &&\
#    php $APP_HOME/artisan route:clear > /dev/stdout &&\
#    php $APP_HOME/artisan optimize > /dev/stdout

USER root
