<?php

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
        $suite = new self('Majisti - All tests');
        
        $suite->addTestSuite(\Majisti\AllTests::suite());
        
        return $suite;
    }
}

AllTests::runAlone();
