<?php

use \Application\App_Exception;
use \Application\Board;

use \Application\Player\Player_Interface;
use \Application\Player\Bot;

class BotTest extends PHPUnit_Framework_TestCase {

	private $playerName   = 'Bot1',
			$playerId     = 1,
			$playerMarker = 'O';

	public function setUp() {
		$this->Bot = new Bot($this->playerName, $this->playerId, $this->playerMarker);
	}

	public function testTriggerTurn() {

	}

	public function testGetName() {
		$this->assertEquals(
			$this->Bot->getName(), $this->playerName);
	}

	public function testGetId() {
		$this->assertEquals(
			$this->Bot->getId(), $this->playerId);
	}

	public function testGetMarker() {
		$this->assertEquals(
			$this->Bot->getMarker(), $this->playerMarker);
	}

	public function testSetMarker() {
		$BotReflection = new ReflectionClass('Application\Player\Bot');
        $_markerReflection = $BotReflection->getProperty('_marker');
        $_markerReflection->setAccessible(true);
		$marker = 'X';

		//Assert the marker isn't equal to what we're going to set it to
		$this->assertNotEquals(
			$_markerReflection->getValue($this->Bot), $marker);

		//Set the marker
		$this->Bot->setMarker($marker);

		//Assert they are now equal
		$this->assertEquals(
			$_markerReflection->getValue($this->Bot), $marker);
	}

    /**
     * @expectedException Application\App_Exception
     */
	public function testSetMarkerLengthException() {
		$twoCharacterString = 'XX';
		$this->Bot->setMarker($twoCharacterString);
	}

}