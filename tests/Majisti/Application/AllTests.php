<?php

namespace Majisti\Application;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - Application - All tests');
        
        $suite->addTest(Addons\AllTests::suite());
        $suite->addTest(Resource\AllTests::suite());
        
        $suite->addTestCase(__NAMESPACE__ . '\BootstrapTest');
        $suite->addTestCase(__NAMESPACE__ . '\ConstantsTest');
        $suite->addTestCase(__NAMESPACE__ . '\LocalesTest');
        
        return $suite;
    }
}

AllTests::runAlone();