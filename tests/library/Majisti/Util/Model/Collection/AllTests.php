<?php
namespace Majisti\Util\Model\Collection;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - Util - Model - Collection - All tests');
        
        $suite->addTestCase(__NAMESPACE__ . '\StackTest');
        
        return $suite;
    }
}

AllTests::runAlone();
