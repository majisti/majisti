<?php
namespace Majisti\Util;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - Util - All tests');
        
        $suite->addTest(Model\AllTests::suite());
        
        return $suite;
    }
}

AllTests::runAlone();
