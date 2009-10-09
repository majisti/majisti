<?php

namespace Majisti\Test;

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
        $suite = new self('Majisti Framework - All tests');
        
        require_once 'library/AllTests.php';
        $suite->addTest(\Majisti\Test\Library\AllTests::suite());
        
        require_once 'integration/AllTests.php';
        $suite->addTest(\Majisti\Test\Integration\AllTests::suite());
        
        return $suite;
    }
}

AllTests::runAlone();
