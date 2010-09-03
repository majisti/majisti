<?php

/*
 * APPLICATION_NAME defines your application namespace which every model
 * class should use. E.g MyProject_Model_Animal. This makes use of Zend's
 * ResourcePlugin where those kind of models can be instanciated without
 * the use of require_once.
 *
 * APPLICATION_ENVIRONMENT defines your application's environments. Majisti
 * supports four kind of environments: production, integration, testing
 * and developement.
 *
 *  production: Where the application hosts actual/real data. No errors
 *              are outputed to the client for maximum security.
 *  integration: Environment that tries to be as close as possible from
 *              the production environment, made for testing and other
 *              application integration.
 *  testing: A testing environment based on the development environment.
 *           The only difference is that the environment is not setup on a
 *           single developer's machine but rather serves as an testing
 *           environment for all developers.
 *  development: The developement environment, usually setup on each developer
 *               workstation.
 */
define('APPLICATION_NAME',          'Majisti');
define('APPLICATION_ENVIRONMENT',   'development');

/* PHP parsers below 5.3.0 will understand this, but die in version compare */
if( version_compare(PHP_VERSION, '5.3.0') < 0 ) {
    die("This project is compatible with PHP 5.3 or higher, your version is: " .
            PHP_VERSION);
}

/*
 * Majisti library folder's name. Will search recursively upright for the folder.
 * No forward nor ending slashes allowed. If you find that it uses too much
 * resources due to an awkward library placement, you can use another path
 * by defining MAJISTI_LIBRARY_PATH and passing it an absolute or relative path
 *
 * Note: uses of symbolic links are recommended
 */
define('MAJISTI_FOLDER_NAME', 'majisti');

/* launches the application */
require_once '../application/Loader.php';
call_user_func('Majisti\Application\Loader::load');
