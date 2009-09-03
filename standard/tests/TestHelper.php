<?php

/**
 * This file should be included with every TestCase.
 *
 * @author Steven Rosato, based on ZF's TestHelper.php
 * 
 * TODO: this file is far for completed or reviewed yet, it works
 * but it is not finished
 */

/* Include PHPUnit dependencies */
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';

if( extension_loaded('xdebug') ) {
    xdebug_disable();
    ini_set('xdebug.collect_params', 0);
    ini_set('xdebug.collect_params', 0);
    ini_set('xdebug.dump.POST', 0);
    ini_set('xdebug.dump.GET', 0);
    ini_set('xdebug.show_local_vars', 0);
}

//$it = new RecursiveDirectoryIterator(realpath(dirname(__FILE__) . '/../library/Majisti/'));
//foreach (new RecursiveIteratorIterator($it) as $file) {
//    if( substr($file->getFileName(), 0, 1) === 'I' ) {
//        print dirname($file->getRealPath()) . '<br>';
//        var_dump(strstr(dirname($file->getRealPath()), '.svn'));// . '<br>';
//        if( strstr(dirname($file->getRealPath()), '.svn') === ' ' ) {
//            print $file->getFileName() . '<br>';
//        }
//    }
//}

//PHPUnit_Util_Filter::addFileToFilter(dirname(__FILE__) . '/../library/Majisti/Config/Handler/IHandler.php', 'INTERFACES');

/* Start output buffering */
//ob_start();

/* Set error reporting to the level to which the code must comply. */
error_reporting( E_ALL | E_STRICT );

/* Determine the root, library, and tests directories of the framework distribution. */
$majistiRoot   = realpath(dirname(__FILE__) . '/../../');

$majistiLaboratoryLibrary = "$majistiRoot/laboratory/library";
$majistiIncubatorLibrary = "$majistiRoot/incubator/library";
$majistiStandardLibrary = "$majistiRoot/standard/library";

$laboratoryTests = "$majistiRoot/laboratory/tests";
$incubatorTests  = "$majistiRoot/incubator/tests";
$standardTests   = "$majistiRoot/standard/tests";

$laboratoryExternals    = "$majistiRoot/laboratory/externals";
$incubatorExternals     = "$majistiRoot/incubator/externals";
$standardExternals      = "$majistiRoot/standard/externals";

//\PHPUnit_Util_Filter::addDirectoryToFilter($zfCoreLibrary);
//\PHPUnit_Util_Filter::addDirectoryToWhitelist($majistiCoreLibrary);

$includePaths = array(
    $majistiRoot,
    $majistiLaboratoryLibrary, $majistiIncubatorLibrary, $majistiStandardLibrary,
    $laboratoryTests, $incubatorTests, $standardTests,
    $laboratoryExternals, $incubatorExternals, $standardExternals,
    get_include_path()
);

set_include_path(implode(PATH_SEPARATOR, $includePaths));

//TODO: define more paths?
define('STANDARD_TESTS_PATH', $standardTests);

require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();

require_once 'Majisti/Loader/Autoloader.php';
$loader->pushAutoloader(new \Majisti\Loader\Autoloader());

unset($majistiRoot, $majistiCoreLibrary, $majistiCoreTests, $pearLibrary, $includePaths);

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

