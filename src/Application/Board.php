<?php

namespace Application;

use Application\App_Exception;

class Board {

    const MIN_TILES = 3,
          MAX_TILES = 10;

    protected $_xTiles,
              $_yTiles;

    protected $_moves = array(array());

    protected $_xSeperator = '|',
              $_ySeperator = '_',
              $_xPadding   = 1,
              $_yPadding   = 1;

    /**
     * Sets up our game board's width and height
     * 
     * @param int $xTiles - How many horizontal tiles to draw
     * @param int $yTiles - How many vertical tiles to draw
     */
    public function __construct($xTiles, $yTiles) {

        if(!is_int($xTiles) || !is_int($yTiles))
            throw new App_Exception('Board __construct arguments must both be int');

        if($xTiles < self::MIN_TILES || $yTiles < self::MIN_TILES)
            throw new App_Exception('Number of tiles must be greater than ' . self::MIN_TILES);

        if($xTiles > self::MAX_TILES || $yTiles > self::MAX_TILES)
            throw new App_Exception('Number of tiles must be less than ' . self::MAX_TILES);

        $this->_xTiles = $xTiles;
        $this->_yTiles = $yTiles;

    }

    public function makeMove(Player $player, $x, $y) {

        if(!is_int($x) || !is_int($y))
            throw new App_Exception('makeMove arguments must both be int');

        if(!$this->_checkBounds($x, $y))
            throw new App_Exception('Your move was outside the bounds of the board');

        if(!$this->_checkMoveNotExists($x, $y))
            throw new App_Exception('That selected tile already contains a move');

        //Make our move, adds player to tile by ID
        $this->_moves[$x][$y] = $player->getId();

    }

    /**
     * Draws out the tiles in ASCII text
     * 
     * @param  bool   $print - Whether it should print or not
     * @return string        - The string containing our game board
     */
    public function drawTiles($print = TRUE) {

    }


    /**
     * Checks if there is a win, a win is determined by the smallest of 
     * the boards tile lengths (x or y)
     * 
     * @return int - The player Id
     */
    public function checkWin() {

    }

    /**
     * Checks if a move is within bounds
     * 
     * @param int $x
     * @param int $y
     * 
     * @return bool
     */
    protected function _checkBounds($x, $y) {

    }

    /**
     * Checks if a tile has already been filled
     */
    protected function _checkMoveNotExists($x, $y) {

    }

}