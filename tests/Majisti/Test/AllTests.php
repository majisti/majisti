<?php
namespace Majisti\Test;

require_once __DIR__ . '/TestHelper.php';

/**
 * @desc 
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
        $suite = new self('Majisti Library - Test - PHPUnit - All tests');
        
        $suite->addTestCase(__NAMESPACE__ . '\TestCaseTest');
        
        return $suite;
    }
}

AllTests::runAlone();
