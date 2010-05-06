<?php
namespace Majisti\Loader;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - Loader - All tests');
        
        $suite->addTestCase(__NAMESPACE__ . '\AutoloaderTest');
        
        return $suite;
    }
}

AllTests::runAlone();
