<?php

namespace Majisti\Test\PHPUnit;

require_once 'TestHelper.php';

/**
 * @desc
 * @author 
 */
class TestSuiteTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;
    
    /**
     * Setups the test case
     */
    public function setUp()
    {
        
    }
}

TestSuiteTest::runAlone();
