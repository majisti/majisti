<?php

namespace MajistiX\Editing;

require_once __DIR__ . '/TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('MajistiX - Editing - Util - All tests');

        $suite->addTestSuite(Filter\AllTests::suite());
        
        return $suite;
    }
}

AllTests::runAlone();
