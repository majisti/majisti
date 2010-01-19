<?php

namespace Majisti;

require_once 'TestHelper.php';

if( !defined('PHPUnit_MAIN_METHOD') ) {
    define("PHPUnit_MAIN_METHOD", false);
}

/**
 * @desc
 * @author
 */
class ApplicationTest extends \Zend_Application_ApplicationTest
{
    static public function runAlone()
    {
        \Majisti\Test\PHPUnit\TestCase::setClass(__CLASS__);
        \Majisti\Test\PHPUnit\TestCase::runAlone();
    }

    /**
     * @desc Setups the test case
     */
    public function setUp()
    {

    }

    public function test__construct()
    {

    }
}

ApplicationTest::runAlone();
