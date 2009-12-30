<?php

namespace Majisti\Controller\Plugin;

require_once 'TestHelper.php';

/**
 * @desc
 * @author 
 */
class I18nTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;
    
    /**
     * Setups the test case
     */
    public function setUp()
    {
        
    }
    
    public function testLocaleIsSwitchedOnPost()
    {
        $this->markTestIncomplete();
    }
}

I18nTest::runAlone();
