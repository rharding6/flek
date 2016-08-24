<?php

require_once "autoloader.php";
require_once "/lib/xsrf.php";
require_once ("/etc/apache2/capstone-mysql/encrypted-config.php");

use Edu\Cnm\Flek;

/**
 * api for signup
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

try{
	//grab the mySQL connection
	$pdo = connectToEncryptedMySQL("/etc/apache2/capstone-mysql/flek.ini");

	//determine which HTTP method was used
	$method = array_key_exists("HTTP_X_HTTP_METHOD", $_SERVER) ? $_SERVER["HTTP_X_HTTP_METHOD"] : $_SERVER
	["REQUEST_METHOD"];
	$reply->method = $method
	if($method === "POST") {
		$requestContent = file_get_contents("php://input");
		$requestObject = json_decode($requestContent);
		//check that the user fields that are required have been sent
		if(empty($requestObject->profileName) === true) {
			throw(new InvalidArgumentException("Must fill in first and last name."));
		} else {
			$profileName = filter_var($requestObject->profileName, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		}
		if(empty($requestObject->profileEmail) === true) {
			throw(new InvalidArgumentException("Must fill in email address."));
		} else {
			$profileEmail = filter_var($requestObject->profileEmail, FILTER_SANITIZE_EMAIL);
		}
		if(empty($requestObject->password) ===true) {
			throw(new InvalidArgumentException("Must fill in password."));
		} else {
			$password = filter_var($requestObject->password, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		}
		if(empty($requestObject->verifyPassword) === true) {
			throw(new InvalidArgumentException("Please verify password."));
		} else {
			$verifyPassword = filter_var($requestObject->verifyPassword, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		}
	}
}