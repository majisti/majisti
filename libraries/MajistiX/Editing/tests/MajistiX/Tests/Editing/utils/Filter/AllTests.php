<?php

namespace MajistiX\Editing\Util\Filter;

require_once __DIR__ . '/TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('MajistiX - Editing - All tests');

        $suite->addTestCase(__NAMESPACE__ . '\DynamicUrlTest');
        $suite->addTestCase(__NAMESPACE__ . '\StaticUrlTest');

        return $suite;
    }
}

AllTests::runAlone();
