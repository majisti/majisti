<?php

namespace Majisti\Controller\Plugin;

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
        $suite = new self('Majisti Framework - Controller - Plugin - All tests');
        
        $suite->addTestSuite(__NAMESPACE__ . '\I18nTest');
        
        return $suite;
    }
}

AllTests::runAlone();
