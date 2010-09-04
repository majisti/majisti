<?php

namespace Majisti\Test;

require_once 'TestHelper.php';

/**
 * @desc Test case for the TestCase. Which one came first,
 * the egg or the chicken?
 *
 * @author Majisti
 */
class TestCaseTest extends \Majisti\Test\TestCase
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
     * @desc Assets that getClass returns the correct class value.
     */
    public function testGetClass()
    {
        $this->assertEquals(__CLASS__, self::getClass());
    }
}

TestCaseTest::runAlone();
