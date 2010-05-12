<?php

namespace Majisti\Test;

/**
 * @desc The test case serves as a simplified manner to extend PHPUnit TestCases.
 * It provides support for single running a test or running it as a part
 * of a TestSuite.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @desc Retrieves the extending class via late static binding.
     * The protected $_class must have been set to __CLASS__, or setClass()
     * but have been used. If not an exception is thrown.
     *
     * @return string The class name
     * @throws Exception if $_class was not set to __CLASS__ in the
     * extending class or if the $_class is null. setClass() can also
     * be used.
     */
    static public function getClass()
    {
        if( __CLASS__ === static::$_class || null === static::$_class ) {
            throw new Exception('You must override with late static binding ' . 
                'the protected static variable $_class with __CLASS__ or ' .
                'through the setClass function');
        }
        
        return static::$_class;
    }

    /**
     * @desc Sets the class attribute
     *
     * @param string $class The class name
     */
    static public function setClass($class)
    {
        static::$_class = $class;
    }

    /**
     * @desc Runs a test alone.
     *
     * @param bool $force [opt; def=false] Force the test to run
     * even if its running as part of a TestSuite.
     * @param array $arguments [opt; def=Runner's default] The Runner's arguments
     */
    static public function runAlone($force = false, $arguments = array())
    {
        if( ((bool)$force || !defined('PHPUNIT_TESTCASE_RUNNING'))
                && 'cli' !== PHP_SAPI )
        {
            if( !count($arguments) ) {
                $arguments = \Majisti\Test\Runner::getDefaultArguments();
            }
            
            \Majisti\Test\Runner::run(
                new TestSuite(static::getClass()), $arguments);
        }
    }
}
