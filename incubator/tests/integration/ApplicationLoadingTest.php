<?php

namespace Majisti\Test\Integration;

require_once 'TestHelper.php';

/**
 * @desc Test case that asserts that the 'Deploy anywhere' works.
 * 
 * @author Steven Rosato 
 */
class ApplicationLoadingTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;
    
    /**
     * Setups the test case
     */
    public function setUp()
    {
        
    }
    
    public function testSimpleDemoLoadsCorrectly()
    {
        $this->markTestIncomplete();
    }
}

ApplicationLoadingTest::runAlone();
