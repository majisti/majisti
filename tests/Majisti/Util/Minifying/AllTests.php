<?php
namespace Majisti\Util\Minifying;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Library - Util - Minifying - All tests');
        
        $suite->addTestCase(__NAMESPACE__ . '\CrockFordTest');

        return $suite;
    }
}

AllTests::runAlone();
