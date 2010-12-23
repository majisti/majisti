<?php

/* make sure PHP below 5.3 will die here */
if( version_compare(PHP_VERSION, '5.3.0') < 0 ) {
    die("This project is compatible with PHP 5.3 or higher, your version is: " .
        PHP_VERSION);
}

require_once '../bootstrap.php';
$appLoader->launchApplication();

unset($appLoader);
