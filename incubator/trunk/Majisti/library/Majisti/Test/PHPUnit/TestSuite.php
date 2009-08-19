<?php

namespace Majisti\Test\PHPUnit;

if( !defined('PHPUNIT_TESTCASE_RUNNING') ) {
    define('PHPUNIT_TESTCASE_RUNNING', 1);
}

class TestSuite extends \PHPUnit_Framework_TestSuite
{
    static public function runAlone($force = false, $arguments = array())
    {
        if( $force || (!defined('PHPUNIT_SUITE_RUNNING') && PHP_SAPI !== 'cli') ) {
            if( !defined('PHPUNIT_SUITE_RUNNING') ) {
                define('PHPUNIT_SUITE_RUNNING', 1);
            }
            $suite = static::suite();
            
            if( null === $suite || !($suite instanceof \PHPUnit_Framework_TestSuite) ) {
                throw new Exception('Suite must be an instance of PHPUnit_Framework_TestSuite');
            }
            
            if( !count($arguments) ) {
                $arguments = \Majisti\Test\PHPUnit\Runner::getDefaultArguments();
            }
            
            \Majisti\Test\PHPUnit\Runner::run($suite, $arguments);
        }
    }
    
    public function addTestCase($testClass)
    {
        $this->addTestSuite($testClass);
    }
    
    /**
     * 
     * @return \PHPUnit_Framework_TestSuite
     */
    static public function suite()
    {
        throw new Exception('This method should be overidden via late static binding');
    }
}