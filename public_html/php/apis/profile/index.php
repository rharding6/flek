<?php

require_once dirname(__DIR__4) . "/vendor/autoload.php";
require_once dirname(__DIR__2) . "/classes/autoload.php";
require_once dirname(__DIR__2) . "/lib/xsrf.php";
require_once("/etc/apache2/flek-mysql/encrypted-config.php");

use Edu\Cnm\Flek;

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
	$pdo = connectToEncryptMySQL("/etc/apache2/flek-mysql/profile.ini");

	// determine which HTTP method was used
	$method = array_key_exists("HTTP_X_HTTP_METHOD", $_SERVER) ? $_SERVER["HTTP_X_HTTP_METHOD"] : $_SERVER("REQUEST_METHOD");

	//sanitize input
	$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
	$name = filter_input(INPUT_GET, "name", FILTER_SANITIZE_STRING);
	$email = filter_input(INPUT_GET, "email", FILTER_SANITIZE_STRING);
	$location = filter_input(INPUT_GET, "location", FILTER_SANITIZE_STRING);
	$profileActivationToken = filter_input(INPUT_GET, "profileActivationToken", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
	$bio = filter_input(INPUT_GET, "bio", FILTER_SANITIZE_STRING);
	// DO WE ALSO GET PASSWORD ?
	//ensure the information is valid
	//make sure the primary key is valid for methods that require it
	if($method === "GET" || $method === "PUT") && (empty($id) === true || $id < 0) {
		throw(new InvalidArgumentException(("id cannot be empty or negative", 405));
}


// restrict to just anyone logged in
	if((empty($_SESSION["profile"]) === false));	{
}

		if(empty($_SESSION["profile"]) === false &&
	$_SESSION["profile"]->getProfileId() === $id);

}
	//make sure the profile name is valid for methods that require it
	//if($method === "GET" || $method === "PUT") && (empty($profile) === true || $profile < 0) {
		//throw(new InvalidArgumentException(("profile cannot be empty or negative", 405));

	//make sure the email is valid for methods that require it
	//if($method === "GET" || $method === "PUT") && (empty($email) === true || $email < 0) {
		//throw(new InvalidArgumentException(("location cannot be empty or negative", 405));


	//----------------------GET---------------------------------

if($method === "GET") {
	// set XSRF cookie
	setXsrfCookie();
	// get a Specific profile by Id
	if(empty ($id) === false) {
		$user = Flek\Profile::getProfileByProfileId($pdo, $id);
		if($profile !== null) {
			$reply->data = $profile;
		}
	}
	//Get profile by Name then update it
	elseif(empty($name) === false) {
		$name = Flek\Profile::getProfileByProfileName($pdo, $name);
		if($profile !== null) {
			$reply->data = $profile;
		}
	}
		//Get profile by Email and then update it
		elseif(empty($email) === false) {
			$email = Flek\Profile::getProfileByProfileEmail($pdo, $email);
			if($profile !== null) {
				$reply->data = $profile;
			}
		}
			//Get All profiles then update it
		} else {
	$profiles = Flek\Profile::getAllProfiles($pdo);
	if($profiles !== null) {
		$reply->data = $profiles;

		//need limit access
		//store and change password
	} //----------------------PUT---------------------------------
	elseif($method === "PUT") ;
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
	//prefrom the put
	if($method === "PUT") ;

	//restrict each user to their account
	if(empty($_SESSION["profile"]) === false || $_SESSION["profile"]->getProfileId() !== $id) {
		throw(new \InvalidArgumentException("You are not allowed to access this profile"));
	}


	//retrieve the profile and update it
	$profile = Flek\Profile::getProfileByProfileId($pdo, $id);
	if($profile === null) {
		throw(new RuntimeException("Profile does not exist"));
	}

	//put new Profile attributes into the profile and update
	$profile->setProfileId($requestObject->profileId);
	$profile->setProfileName($requestObject->profileName);
	$profile->setProfileEmail($requestObject->profileEmail);
	$profile->setProfileLocation($requestObject->profileLocation);
	$profile->setProfileBio($requestObject->profileBio);

	if($requstObject->profilePassword !== null) {
		$hash = hash_pbkdf2("sha256", $requestObject->getProfileId, $profile->getProfileSalt(), 262144);
		$profile->setProfileHash($hash);
	}
	$profile->update($pdo);


	//update reply
	$reply->message = "Profile updated ok";
}	else {
	throw(new InvalidArgumentException("Invalid HTTP method request"));
}
	//update reply with exception information
}	catch(Exception $exception) {
		$reply->status = $exception-.getCode();
		$reply->message = $exception



	}
}

