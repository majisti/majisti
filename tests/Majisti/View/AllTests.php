<?php
namespace Majisti\View;

require_once __DIR__ . '/TestHelper.php';

/**
 * @desc
 * @author
 */
class AllTests extends \Majisti\Test\TestSuite
{
    /**
     * @desc Runs all the tests for this directory, running all
     * tests cases and AllTest classes under the first level
     * of directories.
     *
     * @return \Majisti\Test\TestSuite
     */
    public static function suite()
    {
        $suite = new self('Majisti Library - View - Helper - All tests');

        $suite->addTest(Helper\AllTests::suite());

        $suite->addTestCase(__NAMESPACE__ . '\ViewTest');

        return $suite;
    }
}

AllTests::runAlone();
