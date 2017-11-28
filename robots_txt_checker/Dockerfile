FROM python:2.7.14-alpine3.6

# We need a bunch of stuff to build psycopg2
RUN apk update && apk add gcc linux-headers musl-dev postgresql-dev

COPY ./requirements.txt /requirements.txt
RUN pip install -r requirements.txt

COPY ./checker.py /checker.py
COPY ./config.ini /config.ini

CMD ["python", "checker.py", "-c", "/config.ini"]
