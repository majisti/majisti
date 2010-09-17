<?php
namespace Majisti\View\Helper;

require_once 'TestHelper.php';

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

        $suite->addTestSuite(Head\AllTests::suite());

        return $suite;
    }
}

AllTests::runAlone();
