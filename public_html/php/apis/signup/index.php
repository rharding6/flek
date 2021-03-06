<?php

/*grabs composers autoload file - this was done in mail scrum meeting*/
//require_once (dirname(__DIR__, 4) . "/vendor/autoload.php");

require_once dirname(__DIR__, 2) . "/classes/autoload.php";
require_once dirname(__DIR__, 2) . "/lib/xsrf.php";
require_once dirname(__DIR__, 2) . "/lib/mail.php";
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");

use Edu\Cnm\Flek\Profile;

/**
 * api for signup
 *
 * @author Christina Sosa <csosa4@cnm.edu>;
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
	$pdo = connectToEncryptedMySQL("/etc/apache2/capstone-mysql/flek.ini");

	//determine which HTTP method was used
	$method = array_key_exists("HTTP_X_HTTP_METHOD", $_SERVER) ? $_SERVER["HTTP_X_HTTP_METHOD"] : $_SERVER
	["REQUEST_METHOD"];
	$reply->method = $method;

	//perform the post
	if($method === "POST") {
		/*verifyXsrf();*/
		$requestContent = file_get_contents("php://input");
		$requestObject = json_decode($requestContent);

		//ensure all required information is entered
		if(empty($requestObject->profileName) === true) {
			throw(new \InvalidArgumentException("Must fill in name.", 405));
		}
		if(empty($requestObject->profileEmail) === true) {
			throw(new \InvalidArgumentException("Must fill in email address."));
		}
		if(empty($requestObject->profileLocation) === true) {
			throw(new \InvalidArgumentException("Must fill in location."));
		}
		if(empty($requestObject->profileBio) === true) {
			throw(new \InvalidArgumentException("Must fill in Bio."));
		}
		if(empty($requestObject->profilePassword) === true) {
			throw(new \InvalidArgumentException("Must fill in password."));
		} else {
			$profilePassword = $requestObject->profilePassword;
		}
		if(empty($requestObject->profileConfirmPassword) === true) {
			throw(new \InvalidArgumentException("Please confirm the password."));
		}
		if($requestObject->profilePassword !== $requestObject->profileConfirmPassword) {
			throw(new \InvalidArgumentException("Password does not match."));
		}

		// access token might not be needed here
		$salt = bin2hex(random_bytes(32));
		$hash = hash_pbkdf2("sha512", $profilePassword, $salt, 262144);
		$profileAccessToken = bin2hex(random_bytes(16));
		$profileActivationToken = bin2hex(random_bytes(16));

		//create a new profile
		$profile = new Profile(null, $requestObject->profileName, $requestObject->profileEmail, $requestObject->profileLocation, $requestObject->profileBio, $hash, $salt, $profileAccessToken, $profileActivationToken);
		$profile->insert($pdo);


//building the activation link that can travel to another server and still work. This is the link that will be clicked to confirm the account.
//FIXME: make sure URL is /public_html/activation/$activation
		$basePath = dirname($_SERVER["SCRIPT_NAME"], 2);
		$urlglue = $basePath . "/activation/?emailActivationToken=$profileActivationToken";
		$confirmLink = "https://" . $_SERVER["SERVER_NAME"] . $urlglue;
		$messageSubject = "Flek Account Activation";
		$message = <<< EOF
<h2>Welcome to Flek!</h2>
<p>Please visit the following URL to set a new password and complete the sign-up process: </p><p><a href="$confirmLink">$confirmLink<a></p>
EOF;
		$response = mailGunslinger("Flek", "gsandoval49@cnm.edu", $requestObject->profileName, $requestObject->profileEmail, $messageSubject, $message);
		// FIXME: $response doesn't actually return "Email sent."

			$reply->message = "Almost done! Please check your email to activate your account.";

	} else {
		throw(new \InvalidArgumentException("Invalid HTTP request.", 405));
	}
} catch(\Exception $exception) {
	$reply->status = $exception->getCode();
	$reply->message = $exception->getMessage();
} catch(\TypeError $typeError) {
	$reply->status = $typeError->getCode();
	$reply->message = $typeError->getMessage();
}
header("Content-type: application/json");
echo json_encode($reply);