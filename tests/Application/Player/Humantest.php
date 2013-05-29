<?php

use \Application\App_Exception;
use \Application\Board;

use \Application\Player\Player_Interface;
use \Application\Player\Human;

class HumanTest extends PHPUnit_Framework_TestCase {

	private $playerName   = 'Human1',
			$playerId     = 0,
			$playerMarker = 'X';

	public function setUp() {
		$this->Human = new Human($this->playerName, $this->playerId, $this->playerMarker);
	}

	public function testTriggerTurn() {
$humanReflection = new ReflectionClass('Application\Player\Human');
        $_markerReflection = $humanReflection->getProperty('_marker');
		//Create a mock for the board
		$Board = $this->getMockBuilder('\Application\Board')
					  ->disableOriginalConstructor()
					  ->getMock(array('toBoardSizeArray'));

		//Set board::toBoardSizeArray to return true
		$Board->expects($this->any())
              ->method('toBoardSizeArray')
              ->will($this->returnValue(TRUE));

        $memStream = fopen('php://memory', 'w');
        fwrite($memStream, '3,3'.PHP_EOL);
        rewind($memStream);

        //Create a stream for the move
        $triggerResult = $this->Human->triggerTurn($Board, $memStream);

        $this->assertTrue($triggerResult);

	}

	public function testGetName() {
		$this->assertEquals(
			$this->Human->getName(), $this->playerName);
	}

	public function testGetId() {
		$this->assertEquals(
			$this->Human->getId(), $this->playerId);
	}

	public function testGetMarker() {
		$this->assertEquals(
			$this->Human->getMarker(), $this->playerMarker);
	}

	public function testSetMarker() {
		$humanReflection = new ReflectionClass('Application\Player\Human');
        $_markerReflection = $humanReflection->getProperty('_marker');
        $_markerReflection->setAccessible(true);
		$marker = 'O';

		//Assert the marker isn't equal to what we're going to set it to
		$this->assertNotEquals(
			$_markerReflection->getValue($this->Human), $marker);

		//Set the marker
		$this->Human->setMarker($marker);

		//Assert they are now equal
		$this->assertEquals(
			$_markerReflection->getValue($this->Human), $marker);
	}

    /**
     * @expectedException Application\App_Exception
     */
	public function testSetMarkerLengthException() {
		$twoCharacterString = 'XX';
		$this->Human->setMarker($twoCharacterString);
	}

}