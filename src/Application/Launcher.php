<?php

namespace Application;

use Application\App_Exception;
use Application\Util\Input_Handler;

class Launcher {

    protected $_terminate      = FALSE,
              $_inputsReceived = 0,
              $_outputsSent    = 0,
              $_startupDataGathered = FALSE,
              $_inputHandler;

    /**
     * Public constructor
     */
    public function __construct() {
        $this->inputHandler = new Input_Handler();
    }

    /**
     * Sets up our application, begins the
     * loop until termination
     */
    public function run() {

        //Gather our startup data so that we can continue
        while(!$this->_startupDataGathered) {
            if($this->_gatherStartupData())
                $this->_startupDataGathered = TRUE;
        }

        //Get the game rolling!
        

        // While the application is not terminated
        while(!$this->_terminate) {

        }

    }

    /**
     * Sets up our game board based on input
     */
    protected function setupBoard() {

    }

    /**
     * Sets up our players based on input
     */
    protected function setupPlayers() {

    }

    /**
     * Asks and gathers our data to startup our application
     */
    protected function _gatherStartupData() {

        /**
         * Our input output array, this is looped through to set the outputs
         * and gather the data for the application.
         */
        $sendReceive = array(
            array(
                'output_message'  => 'Welcome to the game, please input your board size (x,y): ',
                'invalid_error'   => "Please enter your two numbers, seperated by a comma (x,y)",
                'handler_method'  => 'toBoardDimensionArray',
                'set_as_variable' => '_boardDimensions'
            ),
            array(
                'output_message'  => 'What is the name of player 1: ',
                'invalid_error'   => "Please enter a string",
                'handler_method'  => 'toPlayerName',
                'set_as_variable' => '_player1Name'
            ),
            array(
                'output_message'  => 'What is the name of player 2: ',
                'invalid_error'   => "Please enter a string",
                'handler_method'  => 'toPlayerName',
                'set_as_variable' => '_player2Name'
            ),
            array(
                'output_message'  => 'Is player 1 a bot? (y/n): ',
                'invalid_error'   => "Please make sure to answer 'y' or 'n' only.",
                'handler_method'  => 'toBool',
                'set_as_variable' => '_player1Bot'
            ),
            array(
                'output_message'  => 'Is player 2 a bot? (y/n): ',
                'invalid_error'   => "Please make sure to answer 'y' or 'n' only.",
                'handler_method'  => 'toBool',
                'set_as_variable' => '_player2Bot'
            ),
        );

        while($this->_inputsReceived < count($sendReceive)) {
            if($this->_outputsSent === $this->_inputsReceived) {
                echo $sendReceive[$this->_outputsSent]['output_message'];
                ++$this->_outputsSent;
            } else {
                $line = trim(fgets(STDIN));
                if($line) {
                    $this->{$sendReceive[$this->_inputsReceived]['set_as_variable']} = $line;
                    ++$this->_inputsReceived;
                }
            }
        }

        return TRUE;

    }

}
