<?php
namespace Majisti\Application\Resource;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - Application - Resource - All tests');
        
        $suite->addTestSuite(__NAMESPACE__ . '\ConfighandlerTest');
        $suite->addTestSuite(__NAMESPACE__ . '\DispatcherTest');
        $suite->addTestSuite(__NAMESPACE__ . '\ViewTest');
        
        return $suite;
    }
}

AllTests::runAlone();
