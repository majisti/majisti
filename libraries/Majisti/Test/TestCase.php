<?php

namespace Majisti\Test;

use \Majisti\Application as Application;

/**
 * @desc The test case serves as a simplified manner to extend PHPUnit TestCases.
 * It provides support for single running a test or running it as a part
 * of a TestSuite.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class TestCase extends \Zend_Test_PHPUnit_ControllerTestCase
{
    static protected $_class;

    /**
     * @var Helper
     */
    static protected $_defaultHelper;

    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * @desc Enables Mvc boostraping for this TestCase
     */
    protected function enableMvc()
    {
        $manager = new Application\Manager($this->getHelper()->getOptions());
        $this->bootstrap = $manager->getApplication();
    }

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
        if( null === static::$_class ) {
            static::$_class = get_called_class();
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

    /**
     * @desc Returns the helper instance
     *
     * @return Helper
     */
    public function getHelper()
    {
        if( null === $this->_helper ) {
            return static::getDefaultHelper();
        }

        return $this->_helper;
    }

    /**
     * @desc Sets the helper instance
     * 
     * @param Helper $helper The helper
     */
    public function setHelper(Helper $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * @desc Returns the default helper. If none was set trought setDefaultHelper,
     * Majisti's default helper singleton instance will be returned
     * 
     * @return Helper
     */
    static public function getDefaultHelper()
    {
        if( null === static::$_defaultHelper ) {
            return new Helper();
        }

        return static::$_defaultHelper;
    }

    /**
     * @desc Sets the default helper for every test case
     *
     * @param Helper $helper The default helper
     */
    static public function setDefaultHelper(Helper $helper)
    {
        static::$_defaultHelper = $helper;
    }
}
