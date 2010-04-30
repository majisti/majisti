<?php

namespace Majisti\Config\Handler;

require_once 'TestHelper.php';

/**
 * @desc Tests the markup class.
 * @author Majisti
 */
class MarkupTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var Markup
     */
    public $handler;

    /**
     * @desc Will become a Zend_Config after setup
     *
     * @var \Zend_Config
     */
    public $config = array(
        'foo' => array(
            'title' => 'Foo Title'
        ),
        'bar' => array(
            'title' => 'Bar Title'
        )
    );

    public function setUp()
    {
        $this->handler  = new Markup();
        $this->config   = new \Zend_Config(
            $this->config, array(
                'allowModifications' => true
            )
        );

        $this->handler->push(\Zend_Markup::factory('Bbcode'));
    }

    /**
     * @desc Asserts that every node that uses markups be replaced
     * with their proper text
     */
    public function testHandleWithNoMarkupSyntaxReturnsSameConfig()
    {
        $config = $this->handler->handle($this->config);
        $this->assertEquals($this->config->toArray(), $config->toArray());
    }

    public function testHandleWithMarkupSyntaxParsesCorrectly()
    {
        $this->config->foo->title = '[b]Foo Title[/b]';

        $config = $this->handler->handle($this->config);
        $this->assertEquals('<strong>Foo Title</strong>', $config->foo->title);
    }
}

MarkupTest::runAlone();
