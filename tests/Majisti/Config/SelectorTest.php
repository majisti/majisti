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

        /* test find with or without default argument */
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
    public function testNotFoundValueWillThrowExceptionIfDefaultArgumentProvided()
    {
        $this->selector->find('void');
    }

    /**
     * @desc Assert null return value does not throw exception and
     * returns indeed null.
     */
    public function testNullReturnValue()
    {
        $this->assertNull($this->selector->find('void', null));
        $this->assertNull($this->selector->find('void', NULL));
    }

    /**
     * @expectedException Exception
     */
    public function testParentElementWillReturnObjectOrArray()
    {
        $selector = $this->selector;

        $this->assertType('\Zend_Config', $selector->find('foo'));
        $this->assertType('array', $selector->find('foo', null, true));

        /* should throw exception */
        $selector->find('void', Selector::VOID, true);
    }
}

SelectorTest::runAlone();
