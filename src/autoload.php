<?php

/**
 * Our autoloader, based on namespace naming
 * conventions with folders this should auto
 * load any class that's called.
 */
function __autoload($class) {
    $filePath = str_replace('\\', '/', $class);
    require "{$filePath}.php";
}