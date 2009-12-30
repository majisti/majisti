<?php
namespace Majisti\Test\PHPUnit;

require_once 'TestHelper.php';

/**
 * @desc 
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
        $suite = new self('Majisti Framework - Test - PHPUnit - All tests');
        
//        $suite->addTest(Folder\AllTests::suite());
        $suite->addTestSuite(__NAMESPACE__ . '\TestCaseTest');
        $suite->addTestSuite(__NAMESPACE__ . '\TestSuiteTest');
        
        return $suite;
    }
}

AllTests::runAlone();
