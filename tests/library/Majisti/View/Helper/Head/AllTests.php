<?php

namespace Majisti\View\Helper\Head;

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
        $suite = new self('Majisti Framework - All tests');

        $suite->addTestCase(__NAMESPACE__ . '\HeadLinkOptimizerTest');

        return $suite;
    }
}

AllTests::runAlone();
