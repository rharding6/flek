<?php

require_once "autoloader.php";
require_once "/lib/xsrf.php";
require_once ("/etc/apache2/capstone-mysql/encrypted-config.php");

use Edu\Cnm\Flek\Activation;

/**
 * api for Activation Token
 *
 * @author Christina Sosa <csosa4@cnm.edu>; referenced Derek Mauldin <derek.e.mauldin@gmail.com>
**/

//verify the session, start if not active
if(session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

//prepare an empty reply
$reply = new stdClass();
$reply->status = 200;
$reply->data = null;

try {
	//grab the mySQL connection
	$pdo =  connectToEncryptedMySQL("/etc/apache2/capstone-mysql/flek.ini");

	//determine which HTTP method was used
	$method = array_key_exists("HTTP_X_METHOD", $_SERVER) ? $_SERVER["HTTP_X_HTTP_METHOD"] : $_SERVER["REQUEST_METHOD"];

	//handle GET request - if id is present, that activation is returned, otherwise all activations are returned
	if($method === "GET") {
		//set XSRF cookie
		setXsrfCookie();
	//get the Sign up based on the given field
	$emailActivationToken = filter_input(INPUT_GET, "id", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($emailActivationToken)) {
			throw(new \RangeException("No Activation Token"));
		}
		$profile = Profile::getProfileByProfileActivationToken($pdo, $emailActivationToken);
		if(empty($user)) {
			throw(new \InvalidArgumentException("No profile for Activation Token"));
		}
		$profile->setProfileActivationToken(null);
		$profile->update($pdo);
	} else {
		throw(new\Exception("Invalid HTTP method"));
	}
	//update reply with exception information
} catch (Exception $exception) {
	$reply->status = $exception->getCode();
	$reply->message = $exception->getMessage();
} catch(\TypeError $typeError) {
	$reply->status = $typeError->getCode();
	$reply->message = $typeError->getMessage();

	header("Content-type: application/json");
	if($reply->data === null) {
		unset($reply->data);
	}
	//encode and return reply to front end caller
	echo json_encode($reply);
}