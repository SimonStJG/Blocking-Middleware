import amqplib.client_0_8 as amqp
import datetime
import hashlib
import hmac
import pytest


@pytest.fixture
def sign():
     def _sign(secret, args):
         return hmac.new(secret.encode('ASCII'),
                  ":".join(args).encode('UTF-8'), hashlib.sha512
          ).hexdigest()
     return _sign


@pytest.fixture
def timestamp():
    return datetime.datetime.utcnow().strftime('%Y-%m-%d %H:%M:%S')


@pytest.fixture(scope='session')
def amqp_channel():
    connection = amqp.Connection(host='localhost',
                                 userid='guest',
                                 password='guest',
                                 virtual_host='/')
    return connection.channel()
