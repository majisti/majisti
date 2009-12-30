<?php
namespace Majisti\Loader;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - Loader - All tests');
        
        $suite->addTestSuite(__NAMESPACE__ . '\AutoloaderTest');
//        $suite->addTestSuite(__NAMESPACE__ . '\PluginLoaderTest');
        
        return $suite;
    }
}

AllTests::runAlone();
