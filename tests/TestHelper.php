<?php

/**
 * This file should be included with every TestCase.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/* disable xdebug */
if( extension_loaded('xdebug') ) {
    xdebug_disable();
}

/* Set error reporting to the level to which the code must comply. */
error_reporting( E_ALL | E_STRICT );

/* Determine the root, library, and tests directories of the library distribution. */
$majistiRoot   = realpath(dirname(__FILE__) . '/../');

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

/* Include PHPUnit dependencies */
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';

/* autoloaders */
require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();

require_once 'Majisti/Loader/Autoloader.php';
$loader->pushAutoloader(new \Majisti\Loader\Autoloader());

PHPUnit_Util_Filter::addDirectoryToFilter($majistiRoot . '/externals');
foreach (array('php', 'phtml') as $suffix) {
    PHPUnit_Util_Filter::addDirectoryToFilter($majistiRoot . '/tests', ".$suffix");
}

/* instanciate a mock application */
define('MAJISTI_FOLDER_NAME', dirname($majistiRoot));
\Majisti\Application::setApplicationPath(
    $majistiRoot . '/tests/library/Majisti/Application/_webroot');
\Majisti\Application::getInstance();
PHPUnit_Util_Filter::addFileToFilter($majistiRoot .
    '/library/Majisti/Application/Constants.php');
\Zend_Session::$_unitTestEnabled = true;

unset($majistiRoot, $loader, $includePaths);

/* be a little bit more verbose according to request param */
$request = new \Zend_Controller_Request_Http();
if( $request->has('verbose') ) {
    \Majisti\Test\PHPUnit\Runner::setDefaultArguments(array(
        'printer' => new \Majisti\Test\PHPUnit\Listener\Simple\Html(null, true)
    ));
}
//ob_start();

//$zfRoot        = dirname(__FILE__) . '/..';
//$zfCoreLibrary = "$zfRoot/library";
//$zfCoreTests   = "$zfRoot/tests";
//
///*
// * Omit from code coverage reports the contents of the tests directory
// */
//foreach (array('php', 'phtml', 'csv') as $suffix) {
//    PHPUnit_Util_Filter::addDirectoryToFilter($zfCoreTests, ".$suffix");
//}
//
///*
// * Prepend the Zend Framework library/ and tests/ directories to the
// * include_path. This allows the tests to run out of the box and helps prevent
// * loading other copies of the framework code and tests that would supersede
// * this copy.
// */
//$path = array(
//    $zfCoreLibrary,
//    $zfCoreTests,
//    get_include_path()
//    );
//set_include_path(implode(PATH_SEPARATOR, $path));
//
///*
// * Load the user-defined test configuration file, if it exists; otherwise, load
// * the default configuration.
// */
//if (is_readable($zfCoreTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php')) {
//    require_once $zfCoreTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php';
//} else {
//    require_once $zfCoreTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php.dist';
//}
//
///**
// * Start output buffering, if enabled
// */
//if (defined('TESTS_ZEND_OB_ENABLED') && constant('TESTS_ZEND_OB_ENABLED')) {
//    ob_start();
//}
//
///*
// * Add Zend Framework library/ directory to the PHPUnit code coverage
// * whitelist. This has the effect that only production code source files appear
// * in the code coverage report and that all production code source files, even
// * those that are not covered by a test yet, are processed.
// */
//if (defined('TESTS_GENERATE_REPORT') && TESTS_GENERATE_REPORT === true &&
//    version_compare(PHPUnit_Runner_Version::id(), '3.1.6', '>=')) {
//    PHPUnit_Util_Filter::addDirectoryToWhitelist($zfCoreLibrary);
//}
//
///*
// * Unset global variables that are no longer needed.
// */
//unset($zfRoot, $zfCoreLibrary, $zfCoreTests, $path);

