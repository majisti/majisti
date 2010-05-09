<?php

namespace Majisti\Test\Library;

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
        $suite = new self('Majisti Framework - All tests');
        
        $suite->addTestSuite(\Majisti\AllTests::suite());
        
        return $suite;
    }
}

AllTests::runAlone();
