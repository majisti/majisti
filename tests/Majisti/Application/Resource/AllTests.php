<?php

namespace Majisti\Application\Resource;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - Application - Resource - All tests');
        
        $suite->addTestCase(__NAMESPACE__ . '\ConfighandlerTest');
        $suite->addTestCase(__NAMESPACE__ . '\DispatcherTest');
        $suite->addTestCase(__NAMESPACE__ . '\I18nTest');
        $suite->addTestCase(__NAMESPACE__ . '\TranslateTest');
        $suite->addTestCase(__NAMESPACE__ . '\LayoutTest');
        $suite->addTestCase(__NAMESPACE__ . '\LocaleTest');
        $suite->addTestCase(__NAMESPACE__ . '\ModelcontainerTest');
        $suite->addTestCase(__NAMESPACE__ . '\ViewTest');
        
        return $suite;
    }
}

AllTests::runAlone();