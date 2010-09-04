<?php
namespace Majisti\Model;

require_once 'TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Library - Model - All tests');

        $suite->addTestSuite(Data\AllTests::suite());
        $suite->addTestSuite(Mail\AllTests::suite());

        $suite->addTestCase(__NAMESPACE__ . '\ContainerTest');

        return $suite;
    }
}

AllTests::runAlone();
