<?php

if( version_compare(PHP_VERSION, '5.3.0') < 0 ) {
	die("This project is compatible with PHP 5.3 or higher, your version is: " . PHP_VERSION);
}

/*
 * Majisti library folder's name. Will search recursively upright for the folder.
 * No forward nor ending slashes allowed
 */
define('MAJISTI_FOLDER_NAME', 'Majisti');

/* include Application class */
require_once '../application/Loader.php';

/* PHP parsers below 5.3.0 will understand this, but die in version compare */
call_user_func('\MyProject\Loader::load');
