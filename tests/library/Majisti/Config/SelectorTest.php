<?php

namespace Majisti\Config;

require_once 'TestHelper.php';

/**
 * @desc Test the selector class.
 * @author Majisti
 */
class SelectorTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;
    
    /**
     * @var \Majisti\Config\Selector
     */
    public $selector;

    /**
     * @var \Zend_Config
     */
    public $config;

    /**
     * @desc Setups the test case
     */
    public function setUp()
    {
        $this->selector = new Selector();
        $this->config   = new \Zend_Config(array());
    }

    public function testFoo()
    {
        $this->markTestIncomplete();
    }
}

SelectorTest::runAlone();
