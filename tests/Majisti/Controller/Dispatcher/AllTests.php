<?php

namespace Majisti\Controller\Dispatcher;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Library - Controller - Dispatcher - All tests');
        
        $suite->addTestCase(__NAMESPACE__ . '\MultipleTest');
        
        return $suite;
    }
}

AllTests::runAlone();
