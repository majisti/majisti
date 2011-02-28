<?php

namespace Majisti\Test\Util;

/**
 * @desc Util class for some server information such as if the cli
 * is running or the phpunit binary was used on the command line interface.
 *
 * @author Majisti
 */
class ServerInfo
{
    /**
     * @desc Returns whether the cli was used or not.
     *
     * @return bool True is the cli was used.
     */
    static public function isCliRunning()
    {
        return 'cli' === php_sapi_name();
    }

    /**
     * @desc Returns whether the phpunit binary was used as the cli command
     * or not.
     *
     * @return bool True if the phpunit binary was used as the cli command.
     */
    static public function isPhpunitRunning()
    {
        if( !static::isCliRunning() ) {
            return false;
        }

        /* phpunit was ran straight from command line */
        if( isset($_SERVER['_']) && false !== strpos($_SERVER['_'], 'phpunit') ) {
            return true;
        }

        if( isset($_SERVER['PATH_TRANSLATED'])
            && false !== strpos($_SERVER['PATH_TRANSLATED'], 'phpunit') )
        {
            return true;
        }

        return false;
    }

    /**
     * @desc Returns whether a test suite is currently running or not.
     *
     * @return bool True if a test suite is currently running.
     */
    static public function isTestSuiteRunning()
    {
        return defined('PHPUNIT_SUITE_RUNNING');
    }

    /**
     * @desc Returns whether a test case is currently running or not.
     *
     * @return bool True if a test case is currently running.
     */
    static public function isTestCaseRunning()
    {
        return defined('PHPUNIT_TESTCASE_RUNNING');
    }

}