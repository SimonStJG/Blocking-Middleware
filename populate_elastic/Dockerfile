ARG db_connection_string

FROM python:2.7.14-alpine3.6

# We need a bunch of stuff to build psycopg2
RUN apk update && apk add gcc linux-headers musl-dev postgresql-dev

COPY ./requirements.txt /requirements.txt
RUN pip install -r requirements.txt

COPY ./populate_elastic.py /populate_elastic.py

# TODO Had to guess the interval here
CMD [ "python", "/populate_elastic.py", "--elastic", "http://elastic:9200", "--interval", "600" ]
