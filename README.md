Blocked.org.uk Middleware
=========================

The brains behind [blocked.org.uk/](https://www.blocked.org.uk/), a tool to check which websites
 are blocked by which ISPs in the UK.  The Blocked Middleware aggregated results from several probes
 which monitor blocked websites on different ISPs lines and presents the data as an HTTP API.
 Take a look at [OrgProbe](https://github.com/openrightsgroup/OrgProbe) as well for the code behind
 the probes which monitor website blocking on specific ISP lines.

# Get involved!

We welcome new contributors especially - we hope you find getting involved both easy and fun. All
 you need to get started is a github account.

Please see our [issues repository](https://github.com/openrightsgroup/cmp-issues) for details on how
 to join in.

# Setup for local Development

Requirements:
* [Docker](https://www.docker.com/)
* [Python 3](https://www.python.org/), to run the integration tests

The Blocking Middleware is currently being converted to run inside [Docker](https://www.docker.com/)
 containers.  Since it's only partially complete, the containers need to be brought up in order, and
 there are still some curious initialization steps (sorry!).

0. Optionally, modify `.env` to contain some super-secret passwords.  Default passwords are in
   there at the moment.
1. Bring up the database `docker-compose up -d db`.  Wait a few seconds for it to
   initialize.
2. Create database schema

        docker container cp db/schema.sql blockingmiddleware_db_1:/schema.sql
        docker exec blockingmiddleware_db_1 psql -f /schema.sql -b BlockingMiddlewareDb postgres

3. Insert some test data (example ISPs and a test user) into the database

        docker container cp integration_test/insert_test_data.sql blockingmiddleware_db_1:/insert_test_data.sql
        docker exec blockingmiddleware_db_1 psql -f /insert_test_data.sql -b BlockingMiddlewareDb postgres

4. Bring up the RabbitMQ instance `docker-compose up -d rabbitmq`
5. Bring up the Apache+PHP webserver `docker-compose up -d --build web`
6. Bring up everything else (`redis`, `elastic`, `robots_txt_checker`, and `metadata_gather`):

        docker-compose up -d redis
        docker-compose up -d elastic
        docker-compose up -d --build robots_txt_checker
        docker-compose up -d --build metadata_gather

Steps 2 and 3 should be skipped if the database volume already exists.

The API is now visible at http://localhost/1.2 and there is an example web client at
 `http://localhost/example-client`.

There are also a bunch of scripts which are run periodically, for example using cron on your docker
host:

        docker exec blockingmiddleware_web_1 php -f /var/www/html/backend/refresh_random_links.php
        docker exec blockingmiddleware_web_1 php -f /var/www/html/backend/process_unblocks.php
        docker exec blockingmiddleware_web_1 php -f /var/www/html/backend/process_results.php
        docker exec blockingmiddleware_web_1 php -f /var/www/html/backend/process_reports.php
        docker exec blockingmiddleware_web_1 php -f /var/www/html/backend/requeue.php

Finally, there is a single docker container which is run periodically, for example using cron on
your docker host:
        docker-compose up --build populate_elastic

## Integration tests

There are some exceptionally minimal integrations tests, which can be run once setup for local
development, via
        pip install -r integration_test/requirements.txt
        python -m pytest integration_test/

These tests are python3 only.

# API and Database Specifications

[API Specification](https://wiki.openrightsgroup.org/wiki/Censorship_Monitoring_Project_API)

[Database Specification](https://wiki.openrightsgroup.org/wiki/Censorship_Monitoring_Project_DB)

# Credits

We reused the following software components to make this:

- @ircmaxell's [password compatibility library](https://github.com/ircmaxell/password_compat) (MIT
  license).
- The [Symfony2](https://github.com/symfony/symfony) PHP web development framework (MIT license).
- The [Silex](https://github.com/silexphp/Silex) PHP micro-framework to develop websites based on
  Symfony2 components (MIT license).
- [PostgreSQL](https://www.postgresql.org) (PostgreSQL license).
- [Docker](https://www.docker.com/) (Apache 2.0 License).
- [php-amqp](http://pecl.php.net/package/amqp) from pecl (PHP license).
- [RabbitMQ](https://www.rabbitmq.com) (Mozilla Public License 1.1).
- [Redis](https://redis.io/) (BSD License).
- [Elastic Search (OSS Version)](https://www.elastic.co/) (Apache 2.0 License).

Thanks!
