<?php
namespace Majisti;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - All tests');
        
        $suite->addTest(Folder\AllTests::suite());
        
        return $suite;
    }
}

AllTests::runAlone();
