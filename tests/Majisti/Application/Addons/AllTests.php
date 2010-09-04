<?php

namespace Majisti\Application\Addons;

require_once 'TestHelper.php';

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
