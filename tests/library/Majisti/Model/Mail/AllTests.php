<?php
namespace Majisti\Model\Mail;

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
        $suite = new self('MajistiP - All tests');

        $suite->addTestCase(__NAMESPACE__ . '\BodyPartialTest');
        $suite->addTestCase(__NAMESPACE__ . '\MessageTest');

        return $suite;
    }
}

AllTests::runAlone();
