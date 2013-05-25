<?php

namespace Application\Player;

use \Application\Player\Player_Interface;
use \Application\Util\Input_Handler;
use \Application\App_Exception;
use \Application\Board;

class Human implements Player_Interface {

	protected $_name,
			  $_uid,
			  $_inputHandler;

	/*
	 * Set up the name and Id of the player
	 */
	public function __construct($name, $uid) {
		$this->_inputHandler = new Input_Handler();
		$this->_name = $name;
		$this->_uid = $uid;
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
	public function triggerTurn(Board $board) {

		$move = array();

		while(empty($move)) {
			//Prompt to get move
			$line = trim(fgets(STDIN));
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
			}
		}

		return $move;
	}

	/**
	 * Returns a name of the player
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Returns a Id of the player
	 */
	public function getId() {
		return $this->_uid;
	}

}