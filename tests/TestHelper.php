<?php

/**
 * @desc This file should be included once with every TestCase.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/* disable xdebug */
if( extension_loaded('xdebug') ) {
    xdebug_disable();
}

/* set error reporting to the level to which the code must comply. */
error_reporting( E_ALL | E_STRICT );

/* set include paths */
$majistiRoot = realpath(dirname(__FILE__) . '/../');
$includePaths = array(
    $majistiRoot,
    "$majistiRoot/externals",
    "$majistiRoot/library",
    "$majistiRoot/tests",
    "$majistiRoot/tests/externals",
    "$majistiRoot/tests/library",
    get_include_path()
);

set_include_path(implode(PATH_SEPARATOR, $includePaths));

/* include PHPUnit dependencies */
$dependencies = array('Framework', 'Framework/IncompleteTestError',
                      'Framework/TestCase', 'Framework/TestSuite',
                      'Runner/Version', 'TextUI/TestRunner', 'Util/Filter');

foreach ($dependencies as $dependency) {
    require_once 'PHPUnit/' . $dependency . '.php';
}

/* autoloaders */
require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();

require_once 'Majisti/Loader/Autoloader.php';
$loader->pushAutoloader(new \Majisti\Loader\Autoloader());

/* instanciate a mock application */
define('MAJISTI_FOLDER_NAME', dirname($majistiRoot));
define('APPLICATION_NAME', 'Majisti_Test');

\Majisti\Application::setApplicationPath(
    $majistiRoot . '/tests/library/Majisti/Application/_webroot');
\Majisti\Application::getInstance();

/* code coverage filtering */
PHPUnit_Util_Filter::addDirectoryToFilter($majistiRoot . '/externals');

foreach (array('php', 'phtml') as $suffix) {
    PHPUnit_Util_Filter::addDirectoryToFilter($majistiRoot . '/tests', ".$suffix");
}

PHPUnit_Util_Filter::addFileToFilter($majistiRoot .
    '/library/Majisti/Application/Constants.php');

/* be a little bit more verbose according to request param */
$request = new \Zend_Controller_Request_Http();
if( $request->has('verbose') ) {
    \Majisti\Test\PHPUnit\Runner::setDefaultArguments(array(
        'printer' => new \Majisti\Test\PHPUnit\Listener\Simple\Html(null, true)
    ));
}

\Zend_Session::$_unitTestEnabled = true;

unset($dependencies, $majistiRoot, $loader, $includePaths, $request);
