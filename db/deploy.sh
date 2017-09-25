#/bin/bash

docker exec $1 psql -f /schema.sql -b BlockingMiddlewareDb postgres
