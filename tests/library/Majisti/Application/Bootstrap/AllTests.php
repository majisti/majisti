<?php

namespace Majisti\Application\Bootstrap;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - Application - Bootstrap - All tests');
        
        $suite->addTestSuite(__NAMESPACE__ . '\BootstrapTest');
        
        return $suite;
    }
}

AllTests::runAlone();
