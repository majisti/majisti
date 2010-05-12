<?php

/**
 * @desc This file should be included once with every TestCase.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/* configure xdebug for performance, if the module is enabled */
if( extension_loaded('xdebug') ) {
    $params = array(
        'xdebug.collect_params'             => 3,
        'xdebug.var_display_max_data'       => 3,
        'xdebug.var_display_max_children'   => 3,
        'xdebug.var_display_max_depth'      => 3,
    );

    foreach ($params as $key => $value) {
       ini_set($key, $value);
    }
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
define('APPLICATION_ENVIRONMENT', 'development');

\Majisti\Application::setApplicationPath(
    $majistiRoot . '/tests/library/Majisti/Application/_webroot');
\Majisti\Application::getInstance()->bootstrap();

/* code coverage filtering */
PHPUnit_Util_Filter::addDirectoryToFilter($majistiRoot . '/externals');

foreach (array('php', 'phtml') as $suffix) {
    PHPUnit_Util_Filter::addDirectoryToFilter($majistiRoot . '/tests', ".$suffix");
}

PHPUnit_Util_Filter::addFileToFilter($majistiRoot .
    '/library/Majisti/Application/Constants.php');

/* be a little bit more verbose according to request param */
$request = new \Zend_Controller_Request_Http();
if( $request->has('verbose') || $request->has('v') ) {
    \Majisti\Test\Runner::setDefaultArguments(array(
        'printer' => new \Majisti\Test\Listener\Simple\Html(null, true)
    ));
}

\Zend_Session::$_unitTestEnabled = true;

unset($dependencies, $majistiRoot, $loader, $includePaths, $request);
