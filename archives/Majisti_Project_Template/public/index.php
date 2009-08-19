<?php

/**
 * init environment mode : 'development' | 'production'
 */
define('ENVIRONMENT_MODE', 'development');
/**
 * init name of lib folder where Zend and Majisti are located (ie: 'lib1.3')
 */
define('LIB_FOLDER', 'lib1.3');


// include boostrap
require_once '../application/common/_bootstrap.php';

// initialize bootstrap
$bootstrap = ProjectName_Bootstrap::getInstance();
$bootstrap->dispatch();