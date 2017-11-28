# Debian-based image
FROM php:5-apache

# libpq-dev required for pgsql
# librabbitmq-dev required for amqp
RUN apt-get update && apt-get install -y \
    libpq-dev \
    librabbitmq-dev \
    supervisor

# Install via docker-php-ext-install when available
RUN docker-php-ext-install pdo pdo_pgsql
RUN pecl install redis-3.1.4 amqp-1.9.3
RUN docker-php-ext-enable redis amqp

RUN a2enmod rewrite

# There is already a default config with the below filename, which
# is symlinked to conf-enabled, we want to overwrite it.
COPY ./docker-php.conf /etc/apache2/conf-available/docker-php.conf

COPY ./api /var/www/html/api
COPY ./backend /var/www/html/backend
COPY ./example-client /var/www/html/example-client
COPY ./config /var/www/html/config

# Along with apache2, we run record_results.php in this container.  Supervisord keeps both alive.
# Since record_results.php requires queues to be created in advance, we use start.sh to do this
# before starting supervisor.
COPY ./supervisor.conf /supervisor.conf
COPY ./start.sh /start.sh

RUN chmod u+x /start.sh

CMD [ "/start.sh" ]
