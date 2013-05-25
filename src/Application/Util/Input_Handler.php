<?php

namespace Application\Util;

use \Application\App_Exception;

class Input_Handler {

    /**
     * Our construct
     */
    public function __construct() {

    }

    /**
     * Converts a string to an array suitible for the
     * input of a board size, i.e. length of 2
     * 
     * @param  string $input
     * @return array
     * 
     * @throws App_Exception if array is not greater than 2
     */
    public function toBoardSizeArray($input) {
        $array = explode(',', $input);

        $cleanArray = array();

        //Check for empty elements
        foreach($array as $key => $value) {
            if($value)
                $cleanArray[] = $value;
        }

        if(count($cleanArray) < 2)
            throw new App_Exception('Input not in the correct format');
        else
            return $cleanArray;

    }

    /**
     * Validates and returns a trimmed string
     * 
     * @param  string $input
     * @return string (trimmed)
     * 
     * @throws App_Exception if input is not string or is null
     */
    public function toString($input) {

        if(is_string($input) && $input)
            return trim($input);
        else
            throw new App_Exception('Input not in the correct format');

    }

    /**
     * Converts strings of y/n/yes/no to a bool
     * 
     * @param  string $input
     * @return bool
     * 
     * @throws App_Exception if input is not y/n/yes/no
     */
    public function toBool($input) {

        if($input === 'y' || $input === 'yes')
            return TRUE;
        else if($input === 'n' || $input === 'no')
            return FALSE;
        else
            throw new App_Exception('Input not in the correct format');

    }


}