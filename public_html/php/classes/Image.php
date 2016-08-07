<?php
namespace Edu\Cnm\flek;

require_once("autoload.php");
/**
 *
 * This image can be a small example when the images are stored in the Profile of the user.
 * @author Diane Peshlakai <dpeshlakai3@cnm.edu>
 * @version 3.0.
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
}