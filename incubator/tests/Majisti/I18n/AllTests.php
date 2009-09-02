<?php

namespace Majisti\I18n;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - I18n - All tests');
        
        $suite->addTestSuite(__NAMESPACE__ . '\I18nTest');
        
        return $suite;
    }
}

AllTests::runAlone();
