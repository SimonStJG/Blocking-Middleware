import datetime
import hashlib
import hmac
import json
import requests
import uuid

from fixtures import sign, timestamp


def test_user_registration(sign, timestamp):
    """Hit register/user then status/user.  Tests web can connect to DB."""

    # Register User
    email = "flimbus.michael{}@fake.domain".format(uuid.uuid4())
    response = requests.post("http://localhost/1.2/register/user", data={
        "email": email,
        "password": "flombus"
    })
    assert response.status_code == 201
    register_response_content = json.loads(response.content)
    assert register_response_content['success']

    # Check user registration
    secret = register_response_content['secret']
    response = requests.get("http://localhost/1.2/status/user", params={
        "email": email,
        "date": timestamp,
        "signature": sign(secret, [email, timestamp])
    })
    assert response.status_code == 200
    assert json.loads(response.content) == {"success": "true", "status": "ok"}
