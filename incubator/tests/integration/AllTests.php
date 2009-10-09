<?php
namespace Majisti\Test\Integration;

require_once 'TestHelper.php';

/**
 * @desc 
 * @author Steven Rosato
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
        $suite = new self('Majisti Framework - Integration - All tests');
        
        require_once 'ApplicationLoadingTest.php';
        $suite->addTestSuite(__NAMESPACE__ . '\ApplicationLoadingTest');
        
        return $suite;
    }
}

AllTests::runAlone();
