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
class TestCase extends \PHPUnit_Framework_TestCase implements Test
{
    static protected $_class;

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
     * @param array $arguments [opt; def=Runner's default] The Runner's arguments
     */
    static public function runAlone($arguments = array())
    {
        Standalone::runAlone(static::getClass(), $arguments);
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function getHelper()
    {
        return \Majisti\Test\Helper::getInstance();
    }
}
