<?php

namespace Majisti\Test\PHPUnit;

/**
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    static protected $_class = __CLASS__;

    static public function getClass()
    {
        if( __CLASS__ === static::$_class || null === static::$_class ) {
            throw new Exception('You must override with late static binding the protected static 
                variable $_class with __CLASS__ or through the setClass function');
        }
        
        return static::$_class;
    }
    
    static public function setClass($class)
    {
        static::$_class = $class;
    }
    
    static public function runAlone($force = false, $arguments = array())
    {
        if( ($force || !defined('PHPUNIT_TESTCASE_RUNNING'))
                && 'cli' !== PHP_SAPI ) {
            if( !count($arguments) ) {
                $arguments = \Majisti\Test\PHPUnit\Runner::getDefaultArguments();
            }
            
            \Majisti\Test\PHPUnit\Runner::run(new TestSuite(static::getClass()), $arguments);
        }
    }
}
