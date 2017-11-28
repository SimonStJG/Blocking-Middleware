import amqplib.client_0_8 as amqp
import datetime
import hashlib
import hmac
import json
import requests
import threading
import uuid
import pytest
import time

from fixtures import sign, timestamp, amqp_channel


PRELOADED_USER = 'example@blocked.org.uk'
PRELOADED_SECRET = 'PAFBjZmUPp4d3XTcookscqndJwo2LlHAWmcg'

def prepareProbe(sign, timestamp):
    response = requests.post("http://localhost/1.2/prepare/probe", data={
        "email": PRELOADED_USER,
        "date": timestamp,
        "signature": sign(PRELOADED_SECRET, [PRELOADED_USER, timestamp])
    })
    assert response.status_code == 200
    content = json.loads(response.content)
    assert content['success'] == True
    return content['probe_hmac']


def register_probe(sign, timestamp, hmac):
    probe_seed = uuid.uuid4().hex
    probe_uuid = hashlib.md5((probe_seed + "-" + hmac).encode('ASCII')).hexdigest()
    response = requests.post("http://localhost/1.2/register/probe", data={
        "email": PRELOADED_USER,
        "probe_seed": probe_seed,
        "probe_uuid": probe_uuid,
        "signature": sign(PRELOADED_SECRET, [probe_uuid]),
        "probe_type": "raspi",
        "country_code": "UK"
    })
    assert response.status_code == 201
    content = json.loads(response.content)
    assert content['success']
    return (probe_uuid, content['secret'])


def test_probe_registration(sign, timestamp):
    """Hit prepare/probe, then register/probe"""

    hmac = prepareProbe(sign, timestamp)
    register_probe(sign, timestamp, hmac)

@pytest.mark.timeout(10)
def test_submit_url(sign, timestamp, amqp_channel):
    """Hit submit/url, then status/url"""

    # Clear the channels
    while True:
        msg = amqp_channel.basic_get(queue='url.EE.org')
        if msg is None:
            break
        amqp_channel.basic_ack(delivery_tag=msg.delivery_tag)

    url = "http://blocked.test/{}".format(uuid.uuid4())

    # Register a probe
    probe_hmac = prepareProbe(sign, timestamp)
    probe_uuid, probe_secret = register_probe(sign, timestamp, probe_hmac)

    # submit/url
    response = requests.post("http://localhost/1.2/submit/url", data={
        "email": PRELOADED_USER,
        "url": url,
        "signature": sign(PRELOADED_SECRET, [url])
    })
    assert response.status_code == 201
    assert {"success": True, "queued": True}.items() <= json.loads(response.content).items()

    # status/url
    response = requests.get("http://localhost/1.2/status/url", params={
        "email": PRELOADED_USER,
        "url": url,
        "signature": sign(PRELOADED_SECRET, [url])
    })
    assert response.status_code == 200
    assert json.loads(response.content) == {
        "success": True,
        "url": url,
        "title": None,
        "results": [],
        "url-status": "ok",
        "categories": [],
        "reports": [],
        "last_report_timestamp": None,
        "blacklisted": False
    }

    # Read message off queue
    while True:
        msg = amqp_channel.basic_get(queue='url.EE.org')
        if msg is not None:
            amqp_channel.basic_ack(delivery_tag=msg.delivery_tag)
            assert {"url":url, "request_id": None}.items() <= json.loads(msg.body).items()
            break

    status = 'blocked'
    config = '20171111'

    # TODO This can't possibly be accurate!
    amqp_channel.basic_publish(msg=amqp.Message(json.dumps({
        'network_name': 'EE',
        'ip_network': '1.2.3.4',
        'url': url,
        'http_status': 200,
        'status': status,
        'probe_uuid': probe_uuid,
        'config': config,
        'category': '',
        'blocktype': '',
        'title': '',
        'remote_ip': '',
        'ssl_verified': None,
        'ssl_fingerprint': None,
        'request_id': None,
        'date': timestamp,
        "signature": sign(probe_secret, [
            probe_uuid,
            url,
            status,
            timestamp,
            config
        ])
    })), exchange='org.blocked', routing_key='results.EE.')

    # Wait for the process_results checker to pick it up
    time.sleep(2)

    # status/url
    response = requests.get("http://localhost/1.2/status/url", params={
        "email": PRELOADED_USER,
        "url": url,
        "signature": sign(PRELOADED_SECRET, [url])
    })
    assert response.status_code == 200
    content = json.loads(response.content)
    assert {
        "success": True,
        "url": url,
        "title": None,
        "url-status": "ok",
        "categories": [],
        "reports": [],
        "last_report_timestamp": None,
        "blacklisted": False
    }.items() <= content.items()
    assert len(content['results']) == 1
    assert content['results'][0].items() >= {'blocktype': '',
        'category': '',
        'isp_active': True,
        'last_report_timestamp': None,
        'network_id': 'EE',
        'network_name': 'EE',
        'status': 'blocked'
    }.items()
