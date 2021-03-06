<?php

namespace Majisti\Controller;

require_once __DIR__ . '/TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Library - Controller - All tests');
        
        $suite->addTestSuite(Dispatcher\AllTests::suite());
        $suite->addTestSuite(Plugin\AllTests::suite());
        $suite->addTestSuite(ActionHelper\AllTests::suite());
        
        return $suite;
    }
}

AllTests::runAlone();
