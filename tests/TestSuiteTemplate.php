<?php

namespace Majisti;

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
        
        $suite->addTest(Folder\AllTests::suite());
        $suite->addTestSuite(__NAMESPACE__ . '\FooTest');
        
        return $suite;
    }
}

AllTests::runAlone();
