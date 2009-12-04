<?php
namespace Majisti\Model;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - Model - All tests');
        
//        $suite->addTest(Application\AllTests::suite());
        
        $suite->addTestSuite(__NAMESPACE__ . '\ContainerTest');
        
        return $suite;
    }
}

AllTests::runAlone();
