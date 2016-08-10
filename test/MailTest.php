<?php

namespace Edu\Cnm\Flek\Test;

use Edu\Cnm\Flek\{
	Mail, Profile
};
//grab the project test parameters
require_once ("MailTest.php");

//grab the class under scrutiny

require_once(dirname(__DIR__) . "/public_html/php/classes/autoload.php");

/**
 *full PHP unit test for the Mail class
 *
 * @author Rob Harding
 * @ver 1.0.0
 */
class MailTest extends FlekTest {
	/**
	 * this is the Mail content
	 */
	protected $VALID_MAILCONTENT = "PHP unit test passing";
	/**
	 * this is the updated Mail content
	 */
	protected $VALID_MAILCONTENT2 = "PHP unit test still passing";
	/**
	 * this is the profile who created/sent the message, this is for foreign key relations
	 */
	protected $sender = null;
	/**
	 * this is the profile who viewed/received the message, this is for foreign key relations
	 */
	protected $receiver = null;
	// create dependent objects before running each test
	public final function setUp(){
	//run the default set up() method first
		parent::setUp();

		//create and insert Sender to own the test Mail
		$this->sender = new Profile(null, "@phpunit", "test@phpunit.de","+12125551212");
		$this->sender->insert($this->getPDO());
	}

	/**
	 * test by inserting a valid message and verify that the actual mySQL data matches
	 *
	 */
	public function testInsertValidMail(){
		//count the number of rows and verify that the actual mySQL data matches
		$numRows = $this->getConnection()->getRowCount("mail");

		//create a new message and insert it to mySQL
		$mail = new Mail(null, $this->sender->getProfileId(),$this->VALID_MAILCONTENT);
		$mail->insert($this->getPDO());
	}
}