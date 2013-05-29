<?php

namespace Application\Player;

use \Application\Player\Player_Interface;
use \Application\Util\Input_Handler;
use \Application\App_Exception;
use \Application\Board;

class Human implements Player_Interface {

	protected $_name,
			  $_uid,
			  $_inputHandler,
			  $_marker = '?';

	/*
	 * Set up the name and Id of the player
	 */
	public function __construct($name, $uid, $marker) {
		$this->_inputHandler = new Input_Handler();
		$this->_name = $name;
		$this->_uid = $uid;
		$this->setMarker($marker);
	}

	/**
	 * Gets the move of the player. If this is a human, it will
	 * prompt for a move from that player. Else if it's a bot
	 * the bot will calculate from here and return.
	 * 
	 * We pass the board object to maintain dependency injection
	 * and keep this testable
	 * 
	 * @param Board $board - Our board object
	 * @return array $move - 2 key array (x,y)
	 */
	public function triggerTurn(Board $board, $stdin = NULL) {
		
        if($stdin === NULL)
            $stdin = STDIN;

		$moveMade = false;

		while(!$moveMade) {
			//Prompt to get move
			$line = trim(fgets($stdin));
			if($line) {
                try {
                    //Handler will throw an exception if invalid
                    $handledInput = 
                        $this->_inputHandler->toBoardSizeArray($line);
                } catch(App_Exception $e) {
                    echo $e->getMessage() . PHP_EOL;
                    continue;
                }

                $move = $handledInput;

                try {
                	//Try and make our move
	                $board->makeMove(
						$this,
			            (int) $move[0]-1, //Move has to be -1 as array starts from 0
			            (int) $move[1]-1  //Move has to be -1 as array starts from 0
						);
	            } catch(App_Exception $e) {
	            	echo $e->getMessage() . ', please try again: ';
	            	continue;
	            }

	            //If we get this far, our moves been made and we can skip the loop
	            $moveMade = TRUE;
			}
		}

		return TRUE;
	}

	/**
	 * Returns a name of the player
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Returns a Id of the player
	 * 
	 * @return int
	 */
	public function getId() {
		return $this->_uid;
	}

	/**
	 * Returns the marker character
	 * 
	 * @return string
	 */
	public function getMarker() {
		return $this->_marker;
	}

	/**
	 * Allows the setting of the marker, which is
	 * a 1 character identified (generally X or O)
	 * 
	 * @param string $marker - The 1 character identifier for player
	 * 
	 * @return bool
	 * 
	 * @throws App_Exception if isn't a string or string length isn't 1
	 */
	public function setMarker($marker) {
		if(is_string($marker) && strlen($marker) === 1)
			$this->_marker = $marker;
		else
			throw new App_Exception('Marker must be only 1 character long');
	}

}