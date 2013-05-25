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
              $_ySeperator = "-",
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

    /**
     * Takes a move for a player, does all of the necessary checks first!
     * 
     * @param Player $player - The use of interface Player
     * @param int $x
     * @param int $y
     */
    public function makeMove(Player $player, $x, $y) {

        if(!is_int($x) || !is_int($y))
            throw new App_Exception('makeMove arguments must both be int');

        if(!$this->_checkBounds($x, $y))
            throw new App_Exception('Your move was outside the bounds of the board');

        if(!$this->_checkMoveNotExists($x, $y))
            throw new App_Exception('That selected tile already contains a move');

        //Make our move, adds player to tile by ID
        $this->_moves[$x][$y] = $player->getId();

        return TRUE;

    }

    /**
     * Draws out the tiles in ASCII text
     * 
     * @param  bool   $print - Whether it should print or not
     * @return string        - The string containing our game board
     */
    public function draw($print = TRUE) {

        $drawText = '';

        for($y = 0; $y < $this->_yTiles; ++$y) {
            //Before drawing horizontal tiles
            
            $xDrawText = '';
            for($x = 0; $x < $this->_xTiles; ++$x) {
                //For each of our horizontal tiles
                
                //Draw our player's move, else blank
                $xDrawText .= ' ';

                if($x < $this->_xTiles-1)
                    $xDrawText .= $this->_wrapStringWithPadding($this->_xSeperator, 'x');

            }

            $drawText .= $xDrawText;

            //After drawing horizontal tiles
            if($y < $this->_yTiles-1) {
                $ySeperator = '';
                for($i = 0; $i < strlen($xDrawText); ++$i) {
                    $ySeperator .= $this->_ySeperator;
                }

                $drawText .= $this->_wrapStringWithPadding($ySeperator, 'y');
            }

        }

        if($print)
            echo $drawText;

        return $drawText;

    }


    /**
     * Checks if there is a win, a win is determined by the smallest of 
     * the boards tile lengths (x or y)
     * 
     * @return int - The player Id
     */
    public function checkWin() {

    }

    protected function _wrapStringWithPadding($string, $direction) {

        $paddingString = '';

        if($direction === 'x') {
            $paddingCharacter = ' ';
            $paddingAmount = $this->_xPadding;
        } else if($direction === 'y') {
            $paddingCharacter = PHP_EOL;
            $paddingAmount = $this->_xPadding;
        }

        for($i = 0; $i < $paddingAmount; ++$i)
            $paddingString .= $paddingCharacter;

        return "{$paddingString}{$string}{$paddingString}";

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