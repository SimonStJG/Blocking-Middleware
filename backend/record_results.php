<?php

# long running MySQL connections
ini_set('mysqli.reconnect',1);

include_once __DIR__ . "/../api/1.2/libs/DB.php";
include_once __DIR__ . "/../api/1.2/libs/amqp.php";
include_once __DIR__ . "/../api/1.2/libs/pki.php";
include_once __DIR__ . "/../api/1.2/libs/exceptions.php";
include_once __DIR__ . "/../api/1.2/libs/services.php";

$ch = amqp_connect();

$ex = new AMQPExchange($ch);
$ex->setName('org.results');

$q = new AMQPQueue($ch);
$q->setName('results');

$conn = new APIDB($dbhost, $dbuser, $dbpass, $dbname);

if (@$ARGV[1] == '--noauth') {
	$auth = false;
} else {
	$auth = true;
}

$processor = new ResultProcessorService(
	$conn,
	new UrlLoader($conn),
	new ProbeLoader($conn),
	new IspLoader($conn),
	);

function process_result($msg, $queue) {
	global $processor, $ex, $auth;

	
	$data = (array)json_decode($msg->getBody());
	$queue->ack($msg->getDeliveryTag());

	error_log("Got result: {$data['probe_uuid']} {$data['date']} {$data['status']}");

	$probe = $processor->probe_loader->load($data['probe_uuid']);
	if ($probe['enabled'] != 1) {
		print "Probe not enabled.\n";
		// drop message on floor
		return true;
	}

	var_dump($data);
	#workaround for unicode encoding bug
	if (is_null($data['url'])) {
		return true;
	}
	

	if ($auth) {
		Middleware::verifyUserMessage(
			implode(":", array(
				$data['probe_uuid'],
				$data['url'],
				$data['status'],
				$data['date'],
				$data['config']
				)
			),
			$probe['secret'],
			$data['signature']
		);
	}
	
	try {
		$processor->process_result($data, $probe);
	} catch (Exception $e) {
		error_log("Caught exception: " . get_class($e));
		error_log("Message was: " . $e->getMessage());
	}

	try {

		$forward = array(
			'url' => $data['url'],
			'network_name' => $data['network_name'],
			'status' => $data['status'],
			'blocktype' => $data['blocktype']
		);
		$ex->publish(json_encode($forward), 
			$msg->getRoutingKey(), AMQP_NOPARAM);

	} catch(Exception $e) {

	}

	return true;

}

$q->consume("process_result");

