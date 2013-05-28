<?php

require_once(dirname(__FILE__) . "/../src/autoload.php");

use \Application\App_Exception;
use \Application\Util\Input_Handler;
use \Application\Board;

use \Application\Player\Human;
use \Application\Player\Bot;

class LauncherTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException App_Exception
     */
    public function test_setupBoard() {

    }

    /**
     * @expectedException App_Exception
     */
    public function test_setupPlayers() {

    }

    public function test_resetStartupData() {

    }

    /**
     * @outputBuffering disabled
     */
    public function test_gatherStartupData() {

    }

}