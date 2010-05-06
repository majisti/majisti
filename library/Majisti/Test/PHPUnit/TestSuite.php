<?php

namespace Majisti\Test\PHPUnit;

if( !defined('PHPUNIT_TESTCASE_RUNNING') ) {
    define('PHPUNIT_TESTCASE_RUNNING', 1);
}

/**
 * @desc TestSuite class for running test suites alone easily.
 *
 * @author Majisti
 */
class TestSuite extends \PHPUnit_Framework_TestSuite
{
    /**
     * @desc Runs the test suites alone, without being part of another
     * suite.
     *
     * @param bool $force Force the test to run even if it is part of another
     * suite.
     * @param array $arguments The runner's arguments
     */
    static public function runAlone($force = false, $arguments = array())
    {
        if( (bool)$force || (!defined('PHPUNIT_SUITE_RUNNING')
            && PHP_SAPI !== 'cli') )
        {
            /* define that we are actually running a suite */
            if( !defined('PHPUNIT_SUITE_RUNNING') ) {
                define('PHPUNIT_SUITE_RUNNING', 1);
            }
            $suite = static::suite();

            /* test suites only */
            if( null === $suite || 
                !($suite instanceof \PHPUnit_Framework_TestSuite) )
            {
                throw new Exception('Suite must be an instance ' .
                    'of PHPUnit_Framework_TestSuite');
            }

            /* runner's arguments */
            if( !count($arguments) ) {
                $arguments = \Majisti\Test\PHPUnit\Runner::getDefaultArguments();
            }
            
            \Majisti\Test\PHPUnit\Runner::run($suite, $arguments);
        }
    }

    /**
     * @desc Adds a TestCase to the suite. Serves as a better method name
     * to avoid confusion.
     *
     * @param PHPUnit_Framework_TestCase $testClass
     */
    public function addTestCase($testClass)
    {
        $this->addTestSuite($testClass);
    }

    /**
     * @desc Runs the suite via late static binding.
     *
     * @return \PHPUnit_Framework_TestSuite
     */
    static public function suite()
    {
        throw new Exception('This method should be overidden ' .
            'via late static binding');
    }
}