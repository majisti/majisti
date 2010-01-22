<?php
namespace Majisti\Mail;

require_once 'TestHelper.php';

/**
 * @desc
 * @author
 */
class AllTests extends \Majisti\Test\PHPUnit\TestSuite
{
    /**
     * @desc Runs all the tests for this directory, running all
     * tests cases and AllTest classes under the first level
     * of directories.
     *
     * @return \Majisti\Test\PHPUnit\TestSuite
     */
    public static function suite()
    {
        $suite = new self('MajistiP - All tests');

        $suite->addTestSuite(__NAMESPACE__ . '\BodyPartialTest');
        $suite->addTestSuite(__NAMESPACE__ . '\MessageTest');

        return $suite;
    }
}

AllTests::runAlone();
