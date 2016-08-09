<?php
namespace Edu\Cnm\flek;

require_once("autoload.php");
/**
 *
 * This image can be a small example when the images are stored in the Profile of the user.
 * @author Diane Peshlakai <dpeshlakai3@cnm.edu>
 * @version 1.0.0
 */
class Image implements \JsonSerializable {
	/*
	id for Image: this is the primary key
	* @var int $imageId
	 */
	private $imageId;
	/*
	 * id of the profile that sent this Image;
	 * @var int $imageProfileId
	 */
	private $imageProfileId;
	/*
	 * textual description of the Image
	 * @var string $imageDescription
	 */
	private $imageDescription;
	/*
	 * url sent when image is uploaded
	 * @var string $imageSecureUrl
	 */
	private $imageSecureUrl;
	/*
	 * id sent to identify the image uploaded
	 * @var string $imagePublicId
	 */
	private $imagePublicId;
	/*
	 * id to identify the genre the image is assigned to
	 * @var string $imageGenreId
	 */
	private $imageGenreId;


	/*
	 * constructor for the Image
	 * @param int|null $newImageId of this Image or null
	 * @param int|null $newImageProfileId of the profile that sent this Image
	 * @param string $newImageDescription string containing acutal profile data
	 * @param string $newImageSecureUrl string containing url of the image uploaded
	 * @param string $newImagePublicId string containing id of the image uploaded
	 * @param string int|null $newImageGenreId of the image genre or null
	 * @throws \InvalidArgumentException if data types are not valid
	 * @throws \RangeException if data values are out of range
	 * @throws \TypeError if data types violate type hints
	 * @throws \Exception if some other exception occurs
	 */
	public function __construct(int $newImageId = null, int $newImageProfileId, string $newImageDescription, string $newImageSecureURL,
										 string $newImagePublicId, int $newImageGenreId = null) {
		try {
			$this->setImageId($newImageId);
			$this->setImageProfileId($newImageProfileId);
			$this->setImageDescription($newImageDescription);
			$this->setImageSecureUrl($newImageSecureURL);
			$this->setImagePublicId($newImagePublicId);
			$this->setImageGenreId($newImageGenreId);
		} catch(\InvalidArgumentException $invalidArgument) {
			//rethrow the exception to the caller
			throw(new \InvalidArgumentException(
				$invalidArgument->getMessage(), 0, $invalidArgument));
		} catch(\RangeException $range) {
			//rethrow the exception to the caller
			throw(new\RangeException($range->getMessage(), 0, $range));
		} catch(\TypeError $typeError) {
			//rethrow the exception to the caller
			throw(new \TypeError($typeError->getMessage(), 0, $typeError));
		} catch(\Exception $exception) {
			//rethrow the exception to the caller
			throw(new\Exception($exception->getMessage(), 0, $exception));
		}
	}

	/*
	 * accessor method for image id
	 * @return int|null value of image id
	 */
	public function getImageId() {
		return ($this->imageId);
	}

	/*
	 * mutator method for image id
	 * @param int|null $newImageId new value of image id
	 * @throws \Range Exception if $newImageId is not positive
	 */
	public function setImageId(int $newImageId = null) {
//if image is null this a new image
		if($newImageId === null) {
			$this->imageId = null;
			return;
		}
		//verify the image id is positive
		if($newImageId <= 0) {
			throw(new \RangeException("image id is not positive"));
		}
		//convert and store the image id
		$this->imageId = $newImageId;
	}

	/*
	 * accessor method for imageProfileId
	 * @return int}null value of imageProfileId
	 */
	public function getImageProfileId() {
		return ($this->imageProfileId);
	}

	/*
	 * mutator method for image profile Id
	 * @param int $newImageProfileId new value of image profile id
	 * @throws \RangeException if @newImageProfileId is not positive
	 */
	public function setImageProfileId(int $newImageProfileId) {
		//verify the image profile id is positive
		if($newImageProfileId <= 0) {
			throw(new \RangeException("image profile id is not positive"));
		}
		//convert and store image profile id
		$this->imageProfileId = $newImageProfileId;
	}

	/*
	 * accessor method for imageDescription
	 * @return string value of image description
	 */
	public function getImageDescription() {
		return ($this->imageDescription);
	}

	/*
	 * mutator method for image Description
	 * @throw method for image Description
	 */
	public function setImageDescription(string $newImageDescription) {
		//verify the image description is secure
		$newImageDescription = trim($newImageDescription);
		$newImageDescription = filter_var($newImageDescription, FILTER_SANITIZE_STRING);
		if(empty($newImageDescription) === true) {
			throw(new \InvalidArgumentException("image description is empty or insecure"));
		}
		// verify the image content will fin in the database
		if(strlen($newImageDescription) > 128) {
			throw(new \RangeException("image description too large"));
		}
		// store the image content
		$this->imageDescription = $newImageDescription;
	}

	/*
	 * accessor method for image secure url
	 * #return string  value of image secure url
	*/
	public function getImageSecureUrl() {
		return ($this->imageSecureUrl);
	}

	/*mutator method for image secure url
	/*
	 * @param string $newImageSecureUrl
	 */
	public function setImageSecureUrl(string $newImageSecureUrl) {
		//verify the image secure url is positive
		if($newImageSecureUrl <= 0) {
			throw(new \RangeException("image secure url is not positive"));
		}
		//convert and store the image secure url
		$this->imageSecureId = $newImageSecureUrl;
	}

	/*
	 * accessor method for image public id
	 * @return string $newImagePublicId
	 */
	public function getImagePublicId() {
		return ($this->imagePublicId);
	}

	/*
	 * mutator method for image public id
	 * @param string imagePublicId
	  * @throw string is not positive and string too long
	 */
	public function setImagePublicId(string $newImagePublicId) {
		//verify the public id is positive/too long
		if($newImagePublicId <= 0) {
			throw(new\RangeException("image public id is too long and not positive"));
		}
		// verify the image content will fin in the database
		if(strlen($newImagePublicId) > 128) {
			throw(new \RangeException("image description too large"));
			//convert and store the image public id
		}
	}

	/*
	 * accessor method for image genre id
	 * @return int|null imageGenreId
	 */
	public function getImageGenreId() {
		return ($this->imageGenreId);
	}

	/*
	 * mutator method for image genre id
	 * @param int new value $newImageGenreId
	 * @throws image genre id not positive
	 */
	public function setImageGenreId(int $newImageGenreId) {
		if($newImageGenreId != 0) {
			throw(new \TypeError("image genre id is not positive"));
			// convert and store the image genre id
		}
		$this->imageGenreId = $newImageGenreId;
	}
}

/*
 * inserts the Image into mySQL
 * @param \PDO $pdo PDO connection object
 * @throws \TypeError if $pdo is not a PDO connection object
 */
		public function insert(\PDO $pdo) {
	//enforce the imageId is null
	if($this->imageId !== null) {
		throw(new \PDOException("not a new image"));
	}
	// create query template
	$query = "INSERT INTO image(imageId, imageProfileId, imageDescription,imageSecureUrl, imagePublicId, 
							imageGenreId) VALUES(:imageProfileId, :imageDescription, :imageSecureUrl, :imagePublicId, :imageGenreId)";
	$statement = $pdo->prepare($query);
	// bind the member variables to the place holders in the template
	$parameters = ["imageProfileId" => $this->imageProfileId, "imageDescription" => $this->imageDescription,
		"imageSecureURl" => $this->imageSecureUrl, "imagePublicId" => $this->imagePublicId, "imageGenreId" => $this->
		imageGenreId];
	$statement->execute($parameters);

	/*
	 * update the null imageId with what mySQL just gave us
	 */
	$this->imageId = intval($pdo->lastInsertId());
}

/*
 * deletes this image from mySQL
 * @param \PDO $pdo PDO connection object
 * @throws \PDOException when mySQL related errors
 * @throws \TypeError if $pdo is no a PDO connection object
 */
		public function delete(\PDO $pdo) {
	//enforce the imageId is not null
	if($this->imageId === null) {
		throw(new \PDOException("unable to delete image that does not exist"));
	}
	/*
	 * create query template
	 */
	$query = "DELETE FROM image WHERE imageId = :imageId";
	$statment = $pdo->prepare($query);

	/*
	 * bind the member variables to the place holder the template
	 */
	$parameters = ["imageId" => $this->imageId];
	$statement->exectute($parameters);
}
	/*
	 * updates this Image in mySQL
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related erros
	 * @throws \TypeError if $pdo is not a PDO coection
	 */
	public function update(\PDO $pdo) {
	//enforce the imageId is not null
	if($this->imageId === null) {
		throw(new \PDOException("unable to update a image that does not exist"));
	}

	// create query template
	$query = "UPDATE image SET imageProfileId = :imageProfileId, imageDescription = :imageDescription, imageSecureURl =
			:imageSecureUrl, imagePublicId = :imagePublicId, imageGenreId = :imageGenreId";
	$statement = $pdo->prepare($query);

	//bind the member variables to the place holders in the template
	$parameters = ["imageProfileId" => $this->imageProfileId, "imageDescription" => $this->imageDescription,
		"imageSecureUrl" => $this->imageSecureUrl, "imagePublicId" => $this->imagePublicId, "imageGenreId" => $this->imageGenreId];

	$statement->execute($parameters);
}

	/*
	 * gets the Image by content
	 * @param /PDO $pdo PDO connection object
	 * @param string $imageDescription image description to search for
	 * @return \SplFixedArray SplFixedArray of Image found
	 * @throws \PDOException when mySQL related erros
	 * @throws \TypeError when variables are not to correct data type
	 */
		public static function getImagebyImageDescription(\PDO, $pdo, string $imageDescription) {
			//sanitize the description before searching
		$imageDescriotion = trim($imageDescription);
		$imageDescriotion = filter_var($imageDescription, FILTER_SANITIZE_STRING);
			if(empty($imageDescription) === true) {
				throw(new \PDOException("image description is invalid"));
			}

		// create query template
		$query = "SELECT imageId, imageProfileId, imageDescription, imageSecureUrl, imagePublicId, imageGenreId
		 	FROM image WHERE imageDecription LIKE :imageDescription";
		$statement = $pdo->prepare($query);















}