<?php

/**
 * This file should be included with every TestCase.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
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

/* Start output buffering */
//ob_start();

/* Set error reporting to the level to which the code must comply. */
error_reporting( E_ALL | E_STRICT );

/* Determine the root, library, and tests directories of the framework distribution. */
$majistiRoot        = realpath(dirname(__FILE__) . '/..');
$majistiLab         = "$majistiRoot/library";
$majistiCoreLibrary = realpath("$majistiRoot/../Majisti/library");
$majistiCoreTests   = "$majistiRoot/tests";
$zfCoreLibrary        = "$majistiRoot/externals";
//$pearLibrary        = "$libRoot/Libraries/PEAR/PEAR";

//\PHPUnit_Util_Filter::addDirectoryToFilter($zfCoreLibrary);
//\PHPUnit_Util_Filter::addDirectoryToWhitelist($majistiCoreLibrary);

$includePaths = array(
    $majistiRoot,
    $majistiLab,
    $majistiCoreLibrary,
    $majistiCoreTests,
    $zfCoreLibrary,
//    $pearLibrary,
    get_include_path()
);

set_include_path(implode(PATH_SEPARATOR, $includePaths));

require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();

require_once 'Majisti/Autoloader.php';
$loader->pushAutoloader(new \Majisti\Autoloader());

unset($majistiRoot, $majistiCoreLibrary, $majistiCoreTests, $pearLibrary, $includePaths);

