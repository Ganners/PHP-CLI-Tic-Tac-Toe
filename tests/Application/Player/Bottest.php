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
		$this->Opponent = new \Application\Player\Human('Human1', 0, 'X');

		//We need a Board object, tightly coupled and difficult to mock
		$this->Board = new Board(3,3);

		//Manually create our array of moves for a 3x3
		$moves = array();
		for($y = 0; $y < 3; ++$y) {
			for($x = 0; $x < 3; ++$x) {
				$moves[] = (object) array(
					'rank'        => NULL,
					'coordinates' => array($x, $y),
				);
			}
		}
		$this->Moves = $moves;
	}

	public function test_calculateMinMax() {

        $_calculateMinMax = new ReflectionMethod(
          $this->Bot, '_calculateMinMax'
        );
        $_calculateMinMax->setAccessible(TRUE);

		$BotReflection = new ReflectionClass('Application\Player\Bot');
        $_gamestate = $BotReflection->getProperty('_gameState');
        $_gamestate->setAccessible(true);

        $gamestate = (object) array(
			'moves'  => $this->Moves,
			);

        //Set the gamestate value
        $_gamestate->setValue($this->Bot, $gamestate);
        
        //Assert some expected results based on boards

        /**
         * Defensive Test 1
         * 
         * Should place a best move of 2,2 to block x from winning
         * 
         * x | o | o       x | o | o
         * ---------       ---------
         * x | x |    ==>  x | x |  
         * ---------       ---------
         * o |   |         o |   | o
         */
        
        $this->Board->clearBoard();
        $this->Board->makeMove($this->Opponent, 0, 0);
        $this->Board->makeMove($this->Opponent, 0, 1);
        $this->Board->makeMove($this->Opponent, 1, 1);

        $this->Board->makeMove($this->Bot, 1, 0);
        $this->Board->makeMove($this->Bot, 2, 0);
        $this->Board->makeMove($this->Bot, 0, 2);

        $bestMove = $_calculateMinMax->invokeArgs($this->Bot, array($this->Board));

        $this->assertEquals(
        	$bestMove->coordinates, array(2,2));

        /**
         * Defensive Test 2
         * 
         * Should place a best move of 0,0 to block x from winning
         * 
         *   |   | o       o |   | o
         * ---------       ---------
         * x | x | o  ==>  x | x | o
         * ---------       ---------
         * o |   | x       o |   | x
         */
        
        $this->Board->clearBoard();
        $this->Board->makeMove($this->Opponent, 2, 0);
        $this->Board->makeMove($this->Opponent, 2, 1);
        $this->Board->makeMove($this->Opponent, 0, 2);

        $this->Board->makeMove($this->Bot, 0, 1);
        $this->Board->makeMove($this->Bot, 1, 1);
        $this->Board->makeMove($this->Bot, 2, 2);

        $bestMove = $_calculateMinMax->invokeArgs($this->Bot, array($this->Board));

        $this->assertEquals(
        	$bestMove->coordinates, array(0,0));

        /**
         * Defensive Test 3
         * 
         * Should place a best move of 1,2 to block x from winning
         * 
         * o | x | o       o | x | o
         * ---------       ---------
         *   | x |    ==>    | x |  
         * ---------       ---------
         *   |   |           | o |  
         */
        
        $this->Board->clearBoard();
        $this->Board->makeMove($this->Opponent, 1, 0);
        $this->Board->makeMove($this->Opponent, 1, 1);

        $this->Board->makeMove($this->Bot, 0, 0);
        $this->Board->makeMove($this->Bot, 2, 0);
        
        $bestMove = $_calculateMinMax->invokeArgs($this->Bot, array($this->Board));

        $this->assertEquals(
        	$bestMove->coordinates, array(1,2));

        /**
         * Offensive Test 1
         * 
         * Should place a best move of 1,2 to block x from winning
         * 
         * o | x | o       o | x | o
         * ---------       ---------
         * o | x | x  ==>  o | x | x
         * ---------       ---------
         *   |   |         O |   |  
         */
        
        $this->Board->clearBoard();
        $this->Board->makeMove($this->Opponent, 1, 0);
        $this->Board->makeMove($this->Opponent, 1, 1);
        $this->Board->makeMove($this->Opponent, 2, 1);

        $this->Board->makeMove($this->Bot, 0, 0);
        $this->Board->makeMove($this->Bot, 0, 1);

        $bestMove = $_calculateMinMax->invokeArgs($this->Bot, array($this->Board));

        $this->assertEquals(
        	$bestMove->coordinates, array(0,2));
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