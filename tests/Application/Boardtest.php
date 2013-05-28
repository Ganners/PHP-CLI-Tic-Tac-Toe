<?php

use \Application\Board;
use \Application\App_Exception;
use \Application\Player\Player_Interface;
use \Application\Player\Human;

class BoardTest extends PHPUnit_Framework_TestCase {

	private $boardWidth  = 3,
			$boardHeight = 3;

	public function setUp() {

		//Set up our default 3x3 grid
		$this->Board = new Board(
			$this->boardHeight,
			$this->boardWidth
			);

		$this->player1 = new Human('Player1', 0, 'O');
		$this->player2 = new Human('Player2', 1, 'X');

	}

    /**
     * @expectedException Application\App_Exception
     */
	public function testConstructMaxRangeException() {

		$maxRange = Board::MAX_TILES;

		$this->Board = new Board(
			$maxRange+1,
			$maxRange+1
			);

	}

    /**
     * @expectedException Application\App_Exception
     */
	public function testConstructMinRangeException() {

		$minRange = Board::MIN_TILES;

		$this->Board = new Board(
			$minRange-1,
			$minRange-1
			);

	}

    /**
     * @expectedException Application\App_Exception
     */
	public function testConstructTypeException() {

		$this->Board = new Board(
			'3',
			'3'
			);

	}

	public function testMakeMove() {

		$this->assertTrue(
			$this->Board->makeMove($this->player1, 0, 0));

		$this->assertTrue(
			$this->Board->makeMove($this->player2, 1, 1));

		$this->assertTrue(
			$this->Board->makeMove($this->player1, 2, 2));

	}

	public function test_checkBounds() {

        $method = new ReflectionMethod(
          $this->Board, '_checkBounds'
        );

        $method->setAccessible(TRUE);

        //True assumptions that are in range
		$this->assertTrue(
			$method->invokeArgs($this->Board, array(0,0)));

		$this->assertTrue(
			$method->invokeArgs($this->Board, array(2,2)));

		//False assumptions that are out of range
		$this->assertFalse(
			$method->invokeArgs($this->Board, array(3,3)));

		$this->assertFalse(
			$method->invokeArgs($this->Board, array(-1,-1)));

	}

	public function test_checkMoveNotExists() {

        $method = new ReflectionMethod(
          $this->Board, '_checkMoveNotExists'
        );

        $method->setAccessible(TRUE);

        //Make some test moves
        $this->Board->makeMove($this->player1, 0, 0);
        $this->Board->makeMove($this->player1, 0, 1);
        $this->Board->makeMove($this->player1, 0, 2);
        $this->Board->makeMove($this->player1, 1, 1);
        $this->Board->makeMove($this->player1, 2, 1);

        //Some moves that haven't already been made
		$this->assertTrue(
			$method->invokeArgs($this->Board, array(2,2)));

		$this->assertTrue(
			$method->invokeArgs($this->Board, array(2,0)));

		//Some moves that have been made already
		$this->assertFalse(
			$method->invokeArgs($this->Board, array(0,0)));

		$this->assertFalse(
			$method->invokeArgs($this->Board, array(0,1)));

	}

    /**
     * @expectedException Application\App_Exception
     */
	public function testMakeMoveTypeException() {
		$this->Board->makeMove($this->player1, 'one', 'two');
	}

    /**
     * @expectedException Application\App_Exception
     */
	public function testMakeMoveRangeException() {
		$this->Board->makeMove($this->player1, 4, 4);
	}

    /**
     * @expectedException Application\App_Exception
     */
	public function testMakeMoveAlreadySetException() {
		$this->Board->makeMove($this->player1, 0, 0);
		$this->Board->makeMove($this->player2, 0, 0);
	}

	public function test_wrapStringWithPadding() {

        $method = new ReflectionMethod(
          $this->Board, '_wrapStringWithPadding'
        );

        $method->setAccessible(TRUE);

        $this->assertEquals(
        	$method->invokeArgs($this->Board, array('TEST', 'x')),
        	' TEST '
        	);

        $this->assertEquals(
        	$method->invokeArgs($this->Board, array('TEST', 'y')),
        	PHP_EOL.'TEST'.PHP_EOL
        	);

	}

	public function testDraw() {

		//Test before making any moves
		$output = $this->Board->draw(FALSE);
		$fixture =        PHP_EOL .
			'  |   |  ' . PHP_EOL .
			'---------' . PHP_EOL .
			'  |   |  ' . PHP_EOL .
			'---------' . PHP_EOL .
			'  |   |  ' . PHP_EOL . 
			              PHP_EOL;

		$this->assertEquals($output, $fixture);

		//Test after player 1 goes in square one
		$this->Board->makeMove($this->player1, 0, 0);
		$output = $this->Board->draw(FALSE);
		$fixture =        PHP_EOL .
			'O |   |  ' . PHP_EOL .
			'---------' . PHP_EOL .
			'  |   |  ' . PHP_EOL .
			'---------' . PHP_EOL .
			'  |   |  ' . PHP_EOL . 
			              PHP_EOL;

		$this->assertEquals($output, $fixture);

		//Test after player 2 goes in the middle
		$this->Board->makeMove($this->player2, 1, 1);
		$output = $this->Board->draw(FALSE);
		$fixture =        PHP_EOL .
			'O |   |  ' . PHP_EOL .
			'---------' . PHP_EOL .
			'  | X |  ' . PHP_EOL .
			'---------' . PHP_EOL .
			'  |   |  ' . PHP_EOL . 
			              PHP_EOL;

		$this->assertEquals($output, $fixture);
	}

	public function testGetWidth() {
		$this->assertEquals(
			$this->Board->getWidth(), $this->boardWidth);
	}

	public function testGetHeight() {
		$this->assertEquals(
			$this->Board->getHeight(), $this->boardHeight);
	}

	public function testCheckNoWin() {

		$this->Board->makeMove($this->player1, 0, 0);
		$this->Board->makeMove($this->player2, 1, 1);
		$this->Board->makeMove($this->player1, 2, 2);

		$this->assertFalse($this->Board->checkWin());

	}

	public function testCheckWinHorizontal() {

		// Check horizontal top
		$this->Board->makeMove($this->player1, 0, 0);
		$this->Board->makeMove($this->player1, 1, 0);
		$this->Board->makeMove($this->player1, 2, 0);

		$this->assertEquals(
			$this->Board->checkWin(), $this->player1);

		$this->Board->clearBoard();

		// Check horizontal middle
		$this->Board->makeMove($this->player2, 0, 1);
		$this->Board->makeMove($this->player2, 1, 1);
		$this->Board->makeMove($this->player2, 2, 1);

		$this->assertEquals(
			$this->Board->checkWin(), $this->player2);

		$this->Board->clearBoard();

		// Check horizontal bottom
		$this->Board->makeMove($this->player1, 0, 2);
		$this->Board->makeMove($this->player1, 1, 2);
		$this->Board->makeMove($this->player1, 2, 2);

		$this->assertEquals(
			$this->Board->checkWin(), $this->player1);

	}

	public function testCheckWinVertical() {

		// Check vertical left
		$this->Board->makeMove($this->player1, 0, 0);
		$this->Board->makeMove($this->player1, 0, 1);
		$this->Board->makeMove($this->player1, 0, 2);

		$this->assertEquals(
			$this->Board->checkWin(), $this->player1);

		$this->Board->clearBoard();

		// Check vertical middle
		$this->Board->makeMove($this->player2, 1, 0);
		$this->Board->makeMove($this->player2, 1, 1);
		$this->Board->makeMove($this->player2, 1, 2);

		$this->assertEquals(
			$this->Board->checkWin(), $this->player2);

		$this->Board->clearBoard();

		// Check vertical right
		$this->Board->makeMove($this->player1, 2, 0);
		$this->Board->makeMove($this->player1, 2, 1);
		$this->Board->makeMove($this->player1, 2, 2);

		$this->assertEquals(
			$this->Board->checkWin(), $this->player1);

	}

	public function testCheckWinDiagonal() {

		// Check vertical left
		$this->Board->makeMove($this->player1, 0, 0);
		$this->Board->makeMove($this->player1, 1, 1);
		$this->Board->makeMove($this->player1, 2, 2);

		$this->assertEquals(
			$this->Board->checkWin(), $this->player1);

	}

	public function testCheckWinAntiDiagonal() {

		// Check vertical left
		$this->Board->makeMove($this->player2, 2, 0);
		$this->Board->makeMove($this->player2, 1, 1);
		$this->Board->makeMove($this->player2, 0, 2);

		$this->assertEquals(
			$this->Board->checkWin(), $this->player2);

	}

	public function testCheckWinUserReturn() {
		
		// Check vertical left
		$this->Board->makeMove($this->player2, 2, 0);
		$this->Board->makeMove($this->player2, 1, 1);
		$this->Board->makeMove($this->player2, 0, 2);

		$this->Board->makeMove($this->player1, 2, 2);
		$this->Board->makeMove($this->player1, 1, 2);
		$this->Board->makeMove($this->player1, 0, 1);

		//We are testing the users reference return
		$this->Board->checkWin($users);

		//Assert that both users with moves are returned in the array
		$this->assertTrue(in_array($this->player2, $users));
		$this->assertTrue(in_array($this->player1, $users));

		//Check that the length of the array was 2
		$this->assertEquals(2, sizeof($users));

	}

}