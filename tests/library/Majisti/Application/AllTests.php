<?php

namespace Majisti\Application;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - Application - All tests');
        
        $suite->addTest(Resource\AllTests::suite());
        
        $suite->addTestSuite(__NAMESPACE__ . '\BootstrapTest');
        $suite->addTestSuite(__NAMESPACE__ . '\ConstantsTest');
        
        return $suite;
    }
}

AllTests::runAlone();
