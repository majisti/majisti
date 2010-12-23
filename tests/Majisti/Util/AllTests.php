<?php
namespace Majisti\Util;

require_once __DIR__ . '/TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Library - Util - All tests');
        
        $suite->addTestSuite(Model\AllTests::suite());
        
        return $suite;
    }
}

AllTests::runAlone();
