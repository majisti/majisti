<?php

namespace Majisti\Config;

require_once 'TestHelper.php';

/**
 * @desc Test the selector class.
 * @author Majisti
 */
class SelectorTest extends \Majisti\Test\TestCase
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
        $this->config   = new \Zend_Config(array(
            'foo' => array(
                'bar' => array(
                    'baz' => 'value1'
                ),
            ),
            'key1' => 'value3'
        ));
        $this->selector = new Selector($this->config);
    }

    /**
     * Asserts the constructor
     */
    public function test__construct()
    {
        $selector = new Selector();

        $this->assertNull($selector->getConfig());

        $selector = new Selector($this->config);
        $this->assertEquals($this->config, $selector->getConfig());
    }

    /**
     * @expectedException \Majisti\Config\Exception
     */
    public function test__constructWithNullConfigShouldThrowException()
    {
        $selector = new Selector();

        $this->assertEquals('foo', $selector->find('foo.bar', 'foo'));
        $selector->find('foo.bar');
    }

    /**
     * @expectedException \Majisti\Config\Exception
     */
    public function testFind()
    {
        $selector = $this->selector;

        $this->assertEquals('value1',   $selector->find('foo.bar.baz'));
        $this->assertEquals('foo',      $selector->find('foo.bar.void', 'foo'));
        $this->assertEquals('value3',   $selector->find('key1'));

        /* throws exception */
        $selector->find('foo.bar.void');
    }

    /**
     * @expectedException \Majisti\Config\Exception
     */
    public function testSingleNonExistantKeyThrowsException()
    {
        $this->selector->find('void');
    }

    /**
     * @expectedException \Majisti\Config\Exception
     */
    public function testNullReturnValueWillThrowException()
    {
        $this->selector->find('void', null);
    }

    /**
     * @expectedException \Majisti\Config\Exception
     */
    public function testNULLReturnValue()
    {
        $this->selector->find('void', NULL);
    }
}

SelectorTest::runAlone();
