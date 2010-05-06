<?php

namespace Majisti\Controller\ActionHelper;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - Controller - ActionHelper - All tests');
        
        $suite->addTestCase(__NAMESPACE__ . '\ModelTest');
        
        return $suite;
    }
}

AllTests::runAlone();
