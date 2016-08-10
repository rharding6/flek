<?php
namespace Edu\Cnm\Flek\DataDesign\Test;

use Edu\Cnm\Flek\DataDesign\Image;

// grab the class under scrutiny
require_once("ImageTest.php");

//grab the class
require_once(dirname(__DIR__) . "/php/classes/autoload.php");

/*
 * Full PHPUnit test for the Image class
 *
 * This is a complete PHPUnit test of the Image class. It is complete because *ALL* mySQL/PDO enabled methods
 * are tested for both invalid and valid inputs.
 *
 * @see Image
 * @author Diane Peshlakai <dpeshlakai3@cnm.edu>
 */
class ImageTest extends DataDesignTest {
	/*
	 * content of the Image
	 * @var int $VALID_IMAGEPROFILEID
	 */
	protected $VALID_IMAGEPROFILEID = "Image uploaded is passing";

	/*
	 * description of the uploaded image
	 * @var string $VALID_IMAGEDESCRIPTION
	 */
	protected $VALID_IMAGEDESCRIPTION = "Image test is still passing!";
	/*
	 * secure url of the image
	 * @var string $VALID_IMAGESECUREURL
	 */
	protected $VALID_SECUREURL = "DKJFKFKJ34245435";

	/*
	 * public id for the image uploaded
	 * @var string $VALID_IMAGEPUBLICID
	 */
	protected $VALID_PUBLICID = "DKJF23409340939404040";

	/*
	 * genre id for image uploaded
	 * @var int $VALID_IMAGEGENREID
	 */
	protected $VALID_GENREID = "Graffiti Artists";

	/*
	 * profile that created the image; this is for the foreign key relations
	 * @var imageProfileId
	 */
	protected $image = null;

	/*
	 *
	 */

	proteted $profile = null;