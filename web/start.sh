#!/bin/sh

php /var/www/html/backend/create_queues.php && supervisord -c /supervisor.conf
