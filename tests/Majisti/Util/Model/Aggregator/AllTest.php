<?php

namespace Majisti\Util\Model\Aggregator;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - Util - Model - All tests');

        $suite->addTestCase(__NAMESPACE__ . '\ConfigTest');
        $suite->addTestCase(__NAMESPACE__ . '\ViewTest');

        return $suite;
    }
}

AllTests::runAlone();