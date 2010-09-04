<?php
namespace Majisti\Loader;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Library - Loader - All tests');
        
        $suite->addTestCase(__NAMESPACE__ . '\AutoloaderTest');
        
        return $suite;
    }
}

AllTests::runAlone();
