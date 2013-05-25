<?php

namespace Application;

use \Application\App_Exception;
use \Application\Util\Input_Handler;
use \Application\Board;

use \Application\Player\Human;
use \Application\Player\Bot;

class Launcher {

    protected $_terminate      = FALSE,
              $_inputsReceived = 0,
              $_outputsSent    = 0,
              $_startupDataGathered = FALSE,
              $_inputHandler;

    protected $_board,
              $_players = array(),
              $_turn = 0;

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
                
                //Work out whose turn it is
                $numPlayers = count($this->_players);

                // This will in theory always return a key that exists in our
                // players array! :-)
                $turnKey = $this->_turn % $numPlayers;

                echo $this->_players[$turnKey]->getName() . ', it is now your turn to make a move!' . PHP_EOL .
                        'To do so please enter the x,y co-ordinates of your move (e.g. 2,1): ';

                if( $this->_players[$turnKey]->triggerTurn($this->_board) ) {
                    if(!$winner = $this->_board->checkWin()) {
                        // Redraw the board
                        $this->_board->draw();
                        ++$this->_turn;
                    } else {
                        //Draw the winning board
                        $this->_board->draw();
                        
                        echo 
"
***************************************************************
***************************************************************

Congratulations " . $winner->getName() . ", you have won!

***************************************************************

Thank you for playing, this game was created by Mark Gannaway

***************************************************************
***************************************************************
";
                        $this->_terminate = TRUE;
                    }
                }

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
     * 
     * @return bool
     * 
     * @todo Make this more dry, e.g. Player factory method
     */
    protected function _setupPlayers() {

        //Sort-of factory for player 1
        if($this->_player1Name) {
            if($this->_player1Bot)
                $this->_players[0] = new Bot($this->_player1Name, 0, 'O');
            else
                $this->_players[0] = new Human($this->_player1Name, 0, 'O');
        }

        //Sort of factory for player 2
        if($this->_player2Name) {
            if($this->_player2Bot)
                $this->_players[1] = new Bot($this->_player2Name, 1, 'X');
            else
                $this->_players[1] = new Human($this->_player2Name, 1, 'X');
        }

        if(count($this->_players) < 2)
            throw new App_Exception('Could not create the minimum number of players to continue');

        return TRUE;
    }

    /**
     * Restarts all of our object data to an early state,
     * before information had been set.
     */
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
