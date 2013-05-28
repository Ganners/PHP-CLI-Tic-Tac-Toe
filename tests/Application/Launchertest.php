<?php

use \Application\Launcher;
use \Application\App_Exception;
use \Application\Util\Input_Handler;
use \Application\Board;

use \Application\Player\Human;
use \Application\Player\Bot;

class LauncherTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $this->Launcher = new Launcher();
    }

    /**
     * @expectedException Application\App_Exception
     */
    public function test_setupBoardException() {
        $method = new ReflectionMethod(
          $this->Launcher, '_setupBoard'
        );

        $method->setAccessible(TRUE);

        $method->invoke($this->Launcher);
    }

    public function test_setupBoard() {
        $this->Launcher->boardDimensions = array(3,3);

        $method = new ReflectionMethod(
          $this->Launcher, '_setupBoard'
        );

        $method->setAccessible(TRUE);

        $result = $method->invoke($this->Launcher);

        $this->assertTrue($result);
    }

    /**
     * @expectedException Application\App_Exception
     */
    public function test_setupPlayers() {

        $method = new ReflectionMethod(
          $this->Launcher, '_setupPlayers'
        );

        $method->setAccessible(TRUE);

        $result = $method->invoke($this->Launcher);

    }

    public function test_resetStartupData() {

        $launcherReflection = new ReflectionClass('Application\Launcher');

        //Our public variables
        $this->Launcher->boardDimensions = array(3,3);
        $this->Launcher->player1Name = 'Test Player 1';
        $this->Launcher->player2Name = 'Test Player 2';
        $this->Launcher->player1Bot = false;
        $this->Launcher->player2Bot = true;

        //Allow access to _inputsReceived
        $inputsReceived = $launcherReflection->getProperty('_inputsReceived');
        $inputsReceived->setAccessible(true);
        $inputsReceived->setValue($this->Launcher, 5);

        //Allow access to _outputsSent
        $outputsSent = $launcherReflection->getProperty('_outputsSent');
        $outputsSent->setAccessible(true);
        $outputsSent->setValue($this->Launcher, 5);

        //Allow access to _outputsSent
        $startupDataGathered = $launcherReflection->getProperty('_startupDataGathered');
        $startupDataGathered->setAccessible(true);
        $startupDataGathered->setValue($this->Launcher, TRUE);
        
        //Allow access to _startupDataGathered
        $startupDataGathered = $launcherReflection->getProperty('_startupDataGathered');
        $startupDataGathered->setAccessible(true);
        $startupDataGathered->setValue($this->Launcher, TRUE);

        //Allow access to _board
        $board = $launcherReflection->getProperty('_board');
        $board->setAccessible(true);
        $board->setValue($this->Launcher, new StdClass);

        $method = new ReflectionMethod(
          $this->Launcher, '_resetStartupData'
        );

        $method->setAccessible(TRUE);

        $method->invoke($this->Launcher);

        //Assert they've been defaulted
        $this->assertFalse(
            isset($this->Launcher->boardDimensions));

        $this->assertFalse(
            isset($this->Launcher->player1Name));

        $this->assertFalse(
            isset($this->Launcher->player2Name));

        $this->assertFalse(
            isset($this->Launcher->player1Bot));

        $this->assertFalse(
            isset($this->Launcher->player2Bot));

        $this->assertEquals(
            $inputsReceived->getValue($this->Launcher), 0);

        $this->assertEquals(
            $outputsSent->getValue($this->Launcher), 0);

        $this->assertFalse(
            $startupDataGathered->getValue($this->Launcher));

        $this->assertEquals(
            $startupDataGathered->getValue($this->Launcher), NULL);


    }

    /**
     * @outputBuffering disabled
     */
    public function test_gatherStartupData() {

        $method = new ReflectionMethod(
          $this->Launcher, '_gatherStartupData'
        );

        $method->setAccessible(TRUE);

        $memStream = fopen('php://memory', 'w');
        fwrite($memStream, '3,3'.PHP_EOL.'Player1'.PHP_EOL.'Player2'.PHP_EOL.'n'.PHP_EOL.'n');
        rewind($memStream);
        
        $method->invokeArgs($this->Launcher, array($memStream));

        $this->assertEquals(
            $this->Launcher->boardDimensions, array(3,3));

        $this->assertEquals(
            $this->Launcher->player1Name, 'Player1');

        $this->assertEquals(
            $this->Launcher->player2Name, 'Player2');

        $this->assertEquals(
            $this->Launcher->player1Bot, FALSE);

        $this->assertEquals(
            $this->Launcher->player2Bot, FALSE);

    }

}