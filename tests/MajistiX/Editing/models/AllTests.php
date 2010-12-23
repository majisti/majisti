<?php

namespace MajistiX\Editing\Model;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('MajistiX - Editing - Models- All tests');
        
        $suite->addTestCase(__NAMESPACE__ . '\ContentTest');
        
        return $suite;
    }
}

AllTests::runAlone();
