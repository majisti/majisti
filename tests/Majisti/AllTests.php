<?php

namespace Majisti;

require_once __DIR__ . '/TestHelper.php';

class AllTests extends Test\TestSuite
{
    public static function suite()
    {
        $suite = new self('Majisti Library - All tests');

        $suite->addTestSuite(Application\AllTests::suite());
        $suite->addTestSuite(Config\AllTests::suite());
        $suite->addTestSuite(Controller\AllTests::suite());
        $suite->addTestSuite(Loader\AllTests::suite());
        $suite->addTestSuite(Model\AllTests::suite());
        $suite->addTestSuite(Test\AllTests::suite());
        $suite->addTestSuite(Util\AllTests::suite());
        $suite->addTestSuite(View\AllTests::suite());

        return $suite;
    }
}

AllTests::runAlone();
