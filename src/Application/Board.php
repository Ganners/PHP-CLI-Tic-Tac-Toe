<?php

namespace Application;

use \Application\App_Exception;
use \Application\Player\Player_Interface;

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
    public function makeMove(Player_Interface $player, $x, $y) {

        if(!is_int($x) || !is_int($y))
            throw new App_Exception('makeMove arguments must both be int');

        if(!$this->_checkBounds($x, $y))
            throw new App_Exception('Your move was outside the bounds of the board');

        if(!$this->_checkMoveNotExists($x, $y))
            throw new App_Exception('That selected tile already contains a move');

        //Make our move, adds player to tile by ID
        $this->_moves[$x][$y] = $player;

        return TRUE;

    }

    /**
     * Draws out the tiles in ASCII text
     * 
     * @param  bool   $print - Whether it should print or not
     * @return string        - The string containing our game board
     */
    public function draw($print = TRUE) {

        $drawText = PHP_EOL;

        for($y = 0; $y < $this->_yTiles; ++$y) {
            //Before drawing horizontal tiles
            
            $xDrawText = '';
            for($x = 0; $x < $this->_xTiles; ++$x) {
                //For each of our horizontal tiles
                
                //Draw our player's move, else blank
                if(isset($this->_moves[$x][$y]) && $this->_moves[$x][$y] instanceof Player_Interface)
                    $xDrawText .= $this->_moves[$x][$y]->getMarker();
                else
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

        //Add on a couple extra new lines
        $drawText .= PHP_EOL . PHP_EOL;

        if($print)
            echo $drawText;

        return $drawText;

    }


    /**
     * Checks if there is a win, a win is determined by the smallest of 
     * the boards tile lengths (x or y)
     * 
     * @return int
     */
    public function checkWin() {

        //This gives us the sequence in a row we need
        $requiredSequence = $this->_xTiles < $this->_yTiles ? $this->_xTiles : $this->_yTiles;

        $scores = array();
        $users = array();

        //First we create our tallys
        for($y = 0; $y < $this->_yTiles; ++$y) {
            for($x = 0; $x < $this->_xTiles; ++$x) {

                //Set a reference for that user for use later on
                if(isset($this->_moves[$x][$y]) && $this->_moves[$x][$y] instanceof Player_Interface) {
                    if(!isset($users[ $this->_moves[$x][$y]->getId() ]))
                        $users[ $this->_moves[$x][$y]->getId() ] = $this->_moves[$x][$y];
                }

                //Check horiztonal
                if(isset($this->_moves[$x][$y]) && $this->_moves[$x][$y] instanceof Player_Interface) {
                    if(isset($scores[ $this->_moves[$x][$y]->getId() ]["y_{$y}"]))
                        ++$scores[ $this->_moves[$x][$y]->getId() ]["y_{$y}"];
                    else
                        $scores[ $this->_moves[$x][$y]->getId() ]["y_{$y}"] = 1;
                }

                //Check vertical
                if(isset($this->_moves[$x][$y]) && $this->_moves[$x][$y] instanceof Player_Interface) {
                    if(isset($scores[ $this->_moves[$x][$y]->getId() ]["x_{$x}"]))
                        ++$scores[ $this->_moves[$x][$y]->getId() ]["x_{$x}"];
                    else
                        $scores[ $this->_moves[$x][$y]->getId() ]["x_{$x}"] = 1;
                }

                //Check diagonal
                if(($y + $requiredSequence - 1) < $this->_yTiles) {
                    if(($x + $requiredSequence - 1) < $this->_xTiles) {
                        for($xy = 0; $xy < $requiredSequence; $xy++) {
                            //This will check the diagonal moves until the end
                            $move = isset($this->_moves[$x+$xy][$y+$xy]) ? $this->_moves[$x+$xy][$y+$xy] : NULL;

                            //Increment player if there's one found on the tile
                            if($move instanceof Player_Interface) {
                                if(isset($scores[ $this->_moves[$x+$xy][$y+$xy]->getId() ]["diag_from_{$x}_{$y}"]))
                                    ++$scores[ $this->_moves[$x+$xy][$y+$xy]->getId() ]["diag_from_{$x}_{$y}"];
                                else
                                    $scores[ $this->_moves[$x+$xy][$y+$xy]->getId() ]["diag_from_{$x}_{$y}"] = 1;
                            }
                        }
                    }
                }

                if(($y + $requiredSequence - 1) < $this->_yTiles) {
                    if(($x - $requiredSequence + 1) >= 0) {
                        for($xy = 0; $xy < $requiredSequence; $xy++) {
                            //This will check the diagonal moves until the end
                            $move = isset($this->_moves[$x-$xy][$y+$xy]) ? $this->_moves[$x-$xy][$y+$xy] : NULL;

                            //Increment player if there's one found on the tile
                            if($move instanceof Player_Interface) {
                                if(isset($scores[ $this->_moves[$x-$xy][$y+$xy]->getId() ]["antidiag_from_{$x}_{$y}"]))
                                    ++$scores[ $this->_moves[$x-$xy][$y+$xy]->getId() ]["antidiag_from_{$x}_{$y}"];
                                else
                                    $scores[ $this->_moves[$x-$xy][$y+$xy]->getId() ]["antidiag_from_{$x}_{$y}"] = 1;
                            }
                        }
                    }
                }

            }
        }

        //Now we loop through our tallies and see if any of them have added up to  our required sequence
        foreach($scores as $userId => $userTally) {
            foreach($userTally as $directionalAxisTally) {
                if($directionalAxisTally === $requiredSequence) {
                    //Loop through our user ID's and return their object
                    if(isset($users[$userId])) {
                        return $users[$userId];
                    }
                }
            }
        }

        return FALSE;


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

        if($x < 0 || $x >= $this->_xTiles)
            return FALSE;

        if($y < 0 || $y >= $this->_yTiles)
            return FALSE;

        return TRUE;

    }

    /**
     * Checks if a tile has already been filled
     * 
     * @param int $x
     * @param int $y
     * 
     * @return bool
     */
    protected function _checkMoveNotExists($x, $y) {
        if(isset($this->_moves[$x][$y]) && $this->_moves[$x][$y] instanceof Player_Interface)
            return FALSE;
        else
            return TRUE;
    }

}