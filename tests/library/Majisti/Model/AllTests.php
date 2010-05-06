<?php
namespace Majisti\Model;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Framework - Model - All tests');

        $suite->addTestSuite(Data\AllTests::suite());
        $suite->addTestSuite(Mail\AllTests::suite());

        $suite->addTestCase(__NAMESPACE__ . '\ContainerTest');

        return $suite;
    }
}

AllTests::runAlone();
