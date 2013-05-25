<?php

namespace Application\Player;

use \Application\Board;

interface Player_Interface {

	/*
	 * Set up the name and Id of the player
	 */
	public function __construct($name, $uid, $marker);

	/**
	 * Gets the move of the player. If this is a human, it will
	 * prompt for a move from that player. Else if it's a bot
	 * the bot will calculate from here and return.
	 * 
	 * We pass the board object to maintain dependency injection
	 * and keep this testable
	 */
	public function triggerTurn(Board $board);

	/**
	 * Returns a name of the player
	 */
	public function getName();

	/**
	 * Returns a Id of the player
	 */
	public function getId();

	/**
	 * Returns the marker character
	 */
	public function getMarker();

	/**
	 * Allows the setting of the marker, which is
	 * a 1 character identified (generally X or O)
	 */
	public function setMarker($marker);

}