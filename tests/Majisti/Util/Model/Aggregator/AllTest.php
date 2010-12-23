<?php

namespace Majisti\Util\Model\Aggregator;

require_once __DIR__ . '/TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Library - Util - Model - All tests');

        $suite->addTestCase(__NAMESPACE__ . '\ConfigTest');
        $suite->addTestCase(__NAMESPACE__ . '\ViewTest');

        return $suite;
    }
}

AllTests::runAlone();
