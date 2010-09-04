<?php
namespace Majisti\Model\Data;

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
        $suite = new self('Majisti Library - All tests');

        $suite->addTestCase(__NAMESPACE__ . '\XmlTest');

        return $suite;
    }
}

AllTests::runAlone();