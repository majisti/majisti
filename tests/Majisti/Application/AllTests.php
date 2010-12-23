<?php

namespace Majisti\Application;

require_once __DIR__ . '/TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Library - Application - All tests');
        
        $suite->addTest(Extension\AllTests::suite());
        $suite->addTest(Resource\AllTests::suite());
        
//        $suite->addTestCase(__NAMESPACE__ . '\ManagerTest');
        $suite->addTestCase(__NAMESPACE__ . '\BootstrapTest');
        $suite->addTestCase(__NAMESPACE__ . '\LocalesTest');
        
        return $suite;
    }
}

AllTests::runAlone();
