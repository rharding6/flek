<?php

require_once(dirname(__DIR__, 2) . "/classes/autoload.php");
require_once(dirname(__DIR__, 2) . "/lib/xsrf.php");
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");

use Edu\Cnm\Flek\Profile;

/**
 * Api for Profile class
 *
 * @author Diane Peshlakai <dpeshlakai3@cnm.edu>
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

	// determine which HTTP method was used
	$method = array_key_exists("HTTP_X_HTTP_METHOD", $_SERVER) ? $_SERVER["HTTP_X_HTTP_METHOD"] : $_SERVER["REQUEST_METHOD"];

	//sanitize input
	$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
	$name = filter_input(INPUT_GET, "name", FILTER_SANITIZE_STRING);
	$email = filter_input(INPUT_GET, "email", FILTER_SANITIZE_STRING);
	$location = filter_input(INPUT_GET, "location", FILTER_SANITIZE_STRING);
	$bio = filter_input(INPUT_GET, "bio", FILTER_SANITIZE_STRING);
	$profileActivationToken = filter_input(INPUT_GET, "activationtoken", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
	$profileAccessToken = filter_input(INPUT_GET, "accesstoken", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

	//ensure the information is valid
	if(($method === "PUT") && (empty($id) === true || $id < 0)) {
		throw(new \InvalidArgumentException("Id cannot be negative or empty", 405));
	} elseif(($method === "POST" || $method === "DELETE")) {
		throw(new \InvalidArgumentException("This action is forbidden", 405));
	}

	//----------------------GET---------------------------------

	if($method === "GET") {
		// set XSRF cookie
		setXsrfCookie("/");
		// get a Specific profile by Id
		if(empty($id) === false) {
			$profile = Edu\Cnm\Flek\Profile::getProfileByProfileId($pdo, $id);
			if($profile !== null) {
				$reply->data = $profile;
			}
		} //Get profile by Email and then update it // TODO
		else if(empty($email) === false) {
			$profile = Edu\Cnm\Flek\Profile::getProfileByProfileEmail($pdo, $email);
			if($profile !== null) {
				$reply->data = $profile;
			}
		} else {
			$profiles = Edu\Cnm\Flek\Profile::getsAllProfiles($pdo);
			if($profiles !== null) {
				$censoredProfiles = [];
				foreach($profiles as $profile) {
					$censored = new stdClass();
					$censored->profileId = $profile->getProfileId();
					$censored->profileBio = $profile->getProfileBio();
					$censored->profileLocation = $profile->getProfileLocation();
					$censored->profileName = $profile->getProfileName();
					$censoredProfiles[] = $censored;
				}
				$reply->data = $censoredProfiles;
			}
		}
		//Get All profiles then update it
		//DONT THINK I NEED THIS
	} //----------------------PUT---------------------------------
	elseif($method === "PUT") {
		verifyXsrf();
		$requestContent = file_get_contents("php://input");
		$requestObject = json_decode($requestContent);

		//make sure profile id is available
		if(empty($requestObject->profileName) === true) {
			throw(new \InvalidArgumentException("No profile id for Profile", 405));
		}

		//make sure profile email is available
		if(empty($requestObject->profileEmail) === true) {
			throw(new \InvalidArgumentException("No profile email for Profile", 405));
		}

		//make sure profile location is available
		if(empty($requestObject->profileLocation) === true) {
			throw(new \InvalidArgumentException("No profile location for Profile", 405));
		}

		//make sure profile bio is available
		if(empty($requestObject->profileBio) === true) {
			throw(new \InvalidArgumentException("No profile biography for Profile", 405));
		}

		//restrict each user to their account
		if(empty($_SESSION["profile"]) === true || $_SESSION["profile"]->getProfileId() !== $id) {
			throw(new \InvalidArgumentException("You are not allowed to access this profile"));
		}


		//retrieve the profile and update it
		$profile = Profile::getProfileByProfileId($pdo, $id);
		if($profile === null) {
			throw(new RuntimeException("Profile does not exist"));
		}

		//put new Profile attributes into the profile and up date
		$profile->setProfileName($requestObject->profileName);
		$profile->setProfileEmail($requestObject->profileEmail);
		$profile->setProfileLocation($requestObject->profileLocation);
		$profile->setProfileBio($requestObject->profileBio);

		if($requestObject->profilePassword !== null && ($requestObject->profileConfirmPassword !== null && $requestObject->profilePassword === $requestObject->profileConfirmPassword)) {
			$hash = hash_pbkdf2("sha512", $requestObject->profilePassword, $profile->getProfileSalt(), 262144);
			$profile->setProfileHash($hash);
		}
		$profile->update($pdo);

		//update reply
		$reply->message = "Profile updated ok";
	}
} //update reply with exception information
catch(Exception $exception) {
	$reply->status = $exception->getCode();
	$reply->message = $exception->getMessage();
	$reply->trace = $exception->getTraceAsString();
} catch(TypeError $typeError) {
	$reply->status = $typeError->getCode();
	$reply->message = $typeError->getMessage();
}

header("Content-type: application/json");
if($reply->data === null) {
	unset($reply->data);
}

//encode and return reply to front end caller
echo json_encode($reply);


