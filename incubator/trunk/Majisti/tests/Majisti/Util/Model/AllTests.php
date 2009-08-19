<?php
namespace Majisti\Util\Model;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - Util - All tests');
        
        $suite->addTestSuite(__NAMESPACE__ . '\StackTest');
        
        return $suite;
    }
}

AllTests::runAlone();
