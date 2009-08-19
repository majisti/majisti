<?php

/**
 * This file should be included with every TestCase.
 *
 * @author Steven Rosato, based on ZF's TestHelper.php
 */

/*
 * Start output buffering
 */
ob_start();

/*
 * Set error reporting to the level to which Zend Framework code must comply.
 */
error_reporting( E_ALL | E_STRICT );

/*
 * Determine the root, library, and tests directories of the framework
 * distribution.
 */
$libRoot   = dirname(__FILE__) . '/..';
$majistiCoreLibrary = "$libRoot/Majisti";
$majistiCoreTests   = "$libRoot/test/Majisti";
$pearLibrary		= "$libRoot/Libraries/PEAR/PEAR";

$includePaths = array(
	$libRoot,
	$majistiCoreLibrary,
	$majistiCoreTests,
	$pearLibrary,
	get_include_path()
);

set_include_path(implode(PATH_SEPARATOR, $includePaths));

require_once 'Zend/Loader.php';
Zend_loader::registerAutoload();

unset($libRoot, $majistiCoreLibrary, $majistiCoreTests, $pearLibrary, $includePaths);

/*
 * Include PHPUnit dependencies
 */
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Runner/Version.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Filter.php';
