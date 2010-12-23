<?php

namespace MajistiX;

require_once __DIR__ . '/TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('MajistiX - All tests');

        $suite->addTestSuite(Editing\AllTests::suite());
        
        return $suite;
    }
}

AllTests::runAlone();
