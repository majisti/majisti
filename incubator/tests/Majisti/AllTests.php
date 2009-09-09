<?php
namespace Majisti;

require_once 'TestHelper.php';

class AllTests extends Test\PHPUnit\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - All tests');
        
        $suite->addTest(Config\AllTests::suite());
        $suite->addTest(Controller\AllTests::suite());
        $suite->addTest(I18n\AllTests::suite());
        $suite->addTest(Loader\AllTests::suite());
        $suite->addTest(Util\AllTests::suite());
        
//        $suite->addTestSuite(__NAMESPACE__ . '\ViewTest');
        
        return $suite;
    }
}

AllTests::runAlone();
