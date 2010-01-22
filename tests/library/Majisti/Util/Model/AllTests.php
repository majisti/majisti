<?php

namespace Majisti\Util\Model;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - Util - Model - All tests');
        
        $suite->addTest(Collection\AllTests::suite());
        
        return $suite;
    }
}

AllTests::runAlone();
