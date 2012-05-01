<?php

require_once ('URLify.php');

class URLifyTest extends PHPUnit_Framework_TestCase {
	function test_filter () {
		$this->assertEquals ('jetudie-le-francais', URLify::filter ('  J\'étudie le français  '));
		$this->assertEquals ('lo-siento-no-hablo-espanol', URLify::filter ('Lo siento, no hablo español.'));
		$this->assertEquals ('f3pws', URLify::filter ('ΦΞΠΏΣ'));
	}
}

?>