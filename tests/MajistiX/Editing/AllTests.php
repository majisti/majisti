<?php

namespace MajistiX\Editing;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('MajistiX - Editing - All tests');
        
        $suite->addTestSuite(Model\AllTests::suite());
        
        return $suite;
    }
}

AllTests::runAlone();
