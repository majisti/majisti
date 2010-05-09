<?php

namespace Majisti\Test\PHPUnit;

require_once 'TestHelper.php';

/**
 * @desc Test case for the TestCase. Which one came first,
 * the egg or the chicken?
 *
 * @author Majisti
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
    }

    /**
     * @desc Assets that getClass returns the correct class value.
     */
    public function testGetClass()
    {
        $this->assertEquals(__CLASS__, self::getClass());
    }
}

TestCaseTest::runAlone();
