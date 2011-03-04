<?php

namespace MajistiX\Editing\Plugin;

require_once __DIR__ . '/TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('MajistiX - Editing - Plugin - All tests');

        $suite->addTestCase(__NAMESPACE__ . '\ContentMonitorTest');
        
        return $suite;
    }
}

AllTests::runAlone();
