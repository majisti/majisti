<?php

namespace Majisti\Test\PHPUnit;

require_once 'TestHelper.php';

/**
 * @desc
 * @author 
 */
class TestCaseTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;
    
    public $testCase;
    
    /**
     * Setups the test case
     */
    public function setUp()
    {
        $testCase = new TestCase();    
    }
    
    /**
     * @expectedException Exception
     */
    public function testGetClassWithNoLateStaticBinding()
    {
        self::$_class = null;
        self::getClass();
        self::$_class = __CLASS__;
    }
}

TestCaseTest::runAlone();
