<?php

namespace Majisti\Test;

use \Majisti\Application as Application,
    \Majisti\Test\Util\ServerInfo;

/**
 * @desc The test case serves as a simplified manner to extend PHPUnit TestCases.
 * It provides support for single running a test or running it as a part
 * of a TestSuite. Moreover, it easily enables Mvc testing with
 * the mvc option set through the helper singleton on by calling enableMvc()
 * in the setUp function.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class TestCase extends \Zend_Test_PHPUnit_ControllerTestCase
{
    /**
     * @var string The resolved class for standalone running
     */
    static protected $_class;

    /**
     * @desc Enables Mvc boostraping for this TestCase
     */
    protected function enableMvc()
    {
        /* won't instanciate on multiple call but will instanciate on each test */
        if( null === $this->bootstrap ) {
            $manager = new Application\Manager($this->getHelper()->getOptions());
            $this->bootstrap = $manager->getApplication();
        }
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function run(\PHPUnit_Framework_TestResult $result = NULL)
    {
        if( $this->getHelper()->getOption('mvc') ) {
            $this->enableMvc();
        }

        return parent::run($result);
    }

    /**
     * @desc Retrieves the extending class via late static binding.
     *
     * @return string The class name
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
        if( (bool)$force || !(ServerInfo::isTestCaseRunning()
                || ServerInfo::isPhpunitRunning()) )
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
        return \Majisti\Test\Helper::getInstance();
    }
}
