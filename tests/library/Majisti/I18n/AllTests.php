<?php

namespace Majisti\I18n;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - I18n - All tests');
        
        $suite->addTestCase(__NAMESPACE__ . '\Locales');
        
        return $suite;
    }
}

AllTests::runAlone();
