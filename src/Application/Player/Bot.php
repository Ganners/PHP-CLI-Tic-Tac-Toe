<?php

namespace Application\Player;

use \Application\Player\Player_Interface;
use \Application\Util\Input_Handler;
use \Application\App_Exception;
use \Application\Board;

class Bot implements Player_Interface {

	protected $_name,
			  $_uid,
			  $_inputHandler,
			  $_marker = '?';

	protected $_totalNodesExpanded = 0,
			  $_levelNodesExpanded = 0,
			  $_maxDepth           = 3,
			  $_bestMove,
			  $_gameState;

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
	public function triggerTurn(Board $board) {

		//Get a list of all possible moves
		$moves = array();
		for($y = 0; $y < $board->getWidth(); ++$y) {
			for($x = 0; $x < $board->getHeight(); ++$x) {
				$moves[] = (object) array(
					'rank'        => 0,
					'coordinates' => array($x, $y),
				);
			}
		}

		$this->_gameState = (object) array(
			'player' => $this,
			'moves'  => $moves,
			'best_move' => array(0,0)
			);

		$move = $this->_calculateMinMax($board);

        try {
        	//Try and make our move
            $board->makeMove(
				$this,
	            (int) $move->coordinates[0], //Move has to be -1 as array starts from 0
	            (int) $move->coordinates[1]  //Move has to be -1 as array starts from 0
				);
        } catch(App_Exception $e) {
        	echo $e->getMessage() . ', please try again: ';
        }

		return TRUE;
	}

	protected function _calculateMinMax(Board $board, $opponent = NULL) {

		$this->_totalNodesExpanded += $this->_levelNodesExpanded;
		$_levelNodesExpanded = 0;
		$bestMove = NULL;

		//Make a copy of the board
		$boardCP = clone $board;

		$player = $opponent ? $opponent : $this;

		//Loop through moves and check what the game state is after the '
		//mock move' has been made
		foreach($this->_gameState->moves as $key => &$move) {
			//Test move on board copy
			try {
				$boardCP->makeMove(
					$player,
					(int) $move->coordinates[0],
					(int) $move->coordinates[1]
					);
			} catch(App_Exception $e) {
				//Move could not be made, implement some handling
				continue;
			}

			//Our move was made successfully, check the board state
			$state = $boardCP->checkWin($users);

			$opponent;
			//Find the user that isn't player (result switches between us an opponent)
			foreach($users as $user) {
				if($user->getId() !== $player->getId())
					$opponent = $user;
			}

			if($state === FALSE) {
				//Draw, no one has won
				$move->rank = 0;
			} else if($state instanceof Player_Interface) {
				//A player has won, determine if us or someone else
				if($state->getId() === $this->getId()) {
					//It matches our ID, we win
					$move->rank = 1;
				} else {
					//It's not us, so we lost :-(
					$move->rank = -1;
				}
			}

			$depthRank = 0;

			// If the move is a draw, look through and check what moves our opponent can make
			// to determine the best of the draws
			if($this->_levelNodesExpanded < $this->_maxDepth && $move->rank === 0) {
				$bestNodeMove = $this->_calculateMinMax($boardCP, $opponent);

				if(isset($bestNodeMove->rank))
					$depthRank = $bestNodeMove->rank;

				++$this->_levelNodesExpanded;
			}

			$move->rank += $depthRank;

			if($bestMove === NULL || $move->rank > $bestMove->rank) {
				var_dump($bestMove);
				$bestMove = $move;
			}

			++$this->_totalNodesExpanded;
		}

		return $bestMove;

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