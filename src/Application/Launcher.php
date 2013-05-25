<?php

namespace Application;

use Application\App_Exception;
use Application\Util\Input_Handler;
use Application\Board;

class Launcher {

    protected $_terminate      = FALSE,
              $_inputsReceived = 0,
              $_outputsSent    = 0,
              $_startupDataGathered = FALSE,
              $_inputHandler;

    protected $_board,
              $_player1,
              $_player2;

    protected $_informationToGather = array(
                array(
                    'output_message'  => 'Welcome to the game, please input your board size (x,y): ',
                    'handler_method'  => 'toBoardSizeArray',
                    'set_as_variable' => '_boardDimensions'
                ),
                array(
                    'output_message'  => 'What is the name of player 1: ',
                    'handler_method'  => 'toString',
                    'set_as_variable' => '_player1Name'
                ),
                array(
                    'output_message'  => 'What is the name of player 2: ',
                    'handler_method'  => 'toString',
                    'set_as_variable' => '_player2Name'
                ),
                array(
                    'output_message'  => 'Is player 1 a bot? (y/n): ',
                    'handler_method'  => 'toBool',
                    'set_as_variable' => '_player1Bot'
                ),
                array(
                    'output_message'  => 'Is player 2 a bot? (y/n): ',
                    'handler_method'  => 'toBool',
                    'set_as_variable' => '_player2Bot'
                ),
            );

    /**
     * Public constructor
     */
    public function __construct() {
        $this->_inputHandler = new Input_Handler();
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
        if($this->_setupBoard() && $this->_setupPlayers()) {

            echo "

********************************************
********************************************
          LAUNCHING TIC TAC TOE
********************************************
********************************************

";

            //Draw our board for the first time
            $this->_board->draw();

            // While the application is not terminated
            while(!$this->_terminate) {

                //This is where we start the turn-based game play

            }
        } else {
            $this->run();
        }

    }

    /**
     * Sets up our game board based on input
     */
    protected function _setupBoard() {
        try {
            $this->_board = new Board(
                (int) $this->_boardDimensions[0],
                (int) $this->_boardDimensions[1]
                );
        } catch(App_Exception $e) {

            //On exception, restart the application
            echo $e->getMessage() . PHP_EOL;
            $this->_resetStartupData();

            return FALSE;
        }
        return TRUE;
    }

    /**
     * Sets up our players based on input
     */
    protected function _setupPlayers() {
        return TRUE;
    }

    protected function _resetStartupData() {
        foreach($this->_informationToGather as $key => $value) {
            if(isset($this->{$value['set_as_variable']}))
                unset($this->{$value['set_as_variable']});
        }
        $this->_inputsReceived = 0;
        $this->_outputsSent = 0;
        $this->_startupDataGathered = FALSE;
        $this->_board = NULL;
    }

    /**
     * Asks and gathers our data to startup our application
     */
    protected function _gatherStartupData() {

        /**
         * Our input output array, this is looped through to set the outputs
         * and gather the data for the application.
         */
        while($this->_inputsReceived < count($this->_informationToGather)) {
            if($this->_outputsSent === $this->_inputsReceived) {
                echo $this->_informationToGather[$this->_outputsSent]['output_message'];
                ++$this->_outputsSent;
            } else {
                $line = trim(fgets(STDIN));
                if($line) {
                    try {
                        //Handler will throw an exception if invalid
                        $handledInput = 
                            $this->_inputHandler->{$this->_informationToGather[$this->_inputsReceived]['handler_method']}($line);
                    } catch(App_Exception $e) {

                        // If we get an exception, display the message and rewind the output
                        // so that it displays again
                        echo $e->getMessage() . PHP_EOL;
                        --$this->_outputsSent;
                        continue;
                    }

                    //Set variable to an object var if it's all good
                    $this->{$this->_informationToGather[$this->_inputsReceived]['set_as_variable']} = $handledInput;
                    ++$this->_inputsReceived;
                }
            }
        }

        return TRUE;

    }

}
