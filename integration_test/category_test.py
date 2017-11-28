import datetime
import hashlib
import hmac
import json
import requests
import uuid

from fixtures import sign, timestamp


# TODO Create these in advance instead
PRELOADED_USER = 'example@blocked.org.uk'
PRELOADED_SECRET = 'PAFBjZmUPp4d3XTcookscqndJwo2LlHAWmcg'


# TODO This is weird behaviour?
def test_random_category(timestamp, sign):
    response = requests.get("http://localhost/1.2/category/random", params={
        'email': PRELOADED_USER,
        'signature': sign(PRELOADED_SECRET, [PRELOADED_USER])
    })
    assert response.status_code == 200
    assert json.loads(response.content) == {'id': None, 'name': None, 'success': True}


def test_specific_category(timestamp, sign):
    response = requests.get("http://localhost/1.2/category/558", params={
        'email': PRELOADED_USER,
        'signature': sign(PRELOADED_SECRET, ['558'])
    })
    assert response.status_code == 404
