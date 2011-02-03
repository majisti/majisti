<?php
namespace Majisti\Model;

require_once __DIR__ . '/TestHelper.php';

class AllTests extends \Majisti\Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Library - Model - All tests');

        $suite->addTestSuite(Mail\AllTests::suite());

        $suite->addTestCase(__NAMESPACE__ . '\ContainerTest');
        $suite->addTestCase(__NAMESPACE__ . '\XmlTest');

        return $suite;
    }
}

AllTests::runAlone();
