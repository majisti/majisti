<?php

namespace Majisti\Application\Extension;

require_once __DIR__ . '/TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Library - Application - All tests');
        
        $suite->addTestCase(__NAMESPACE__ . '\ManagerTest');
        
        return $suite;
    }
}

AllTests::runAlone();
