<?php

namespace Majisti\Controller\Dispatcher;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - Controller - Dispatcher - All tests');
        
        $suite->addTestSuite(__NAMESPACE__ . '\MultipleTest');
        
        return $suite;
    }
}

AllTests::runAlone();
