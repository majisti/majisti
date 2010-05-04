<?php

namespace Majisti\Controller;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - Controller - All tests');
        
        $suite->addTest(Dispatcher\AllTests::suite());
        $suite->addTest(Plugin\AllTests::suite());
        $suite->addTest(ActionHelper\AllTests::suite());
        
        return $suite;
    }
}

AllTests::runAlone();
