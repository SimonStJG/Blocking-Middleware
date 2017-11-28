import datetime
import hashlib
import hmac
import json
import requests
import uuid

from fixtures import sign, timestamp


def test_config():
    response = requests.get("http://localhost/1.2/config/2014041903")
    assert response.status_code == 200
    assert 'rules' in json.loads(response.content)
