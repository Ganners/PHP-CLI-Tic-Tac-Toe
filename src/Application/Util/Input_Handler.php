<?php

namespace Application\Util;

use Application\App_Exception;

class Input_Handler {

    public function __construct() {

    }

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

    public function toString($input) {

        if(is_string($input) && $input)
            return trim($input);
        else
            throw new App_Exception('Input not in the correct format');

    }

    public function toBool($input) {

        if($input === 'y' || $input === 'yes')
            return TRUE;
        else if($input === 'n' || $input === 'no')
            return FALSE;
        else
            throw new App_Exception('Input not in the correct format');

    }


}