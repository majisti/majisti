<?php

namespace Majisti\Config\Handler;

require_once 'TestHelper.php';

class MarkupTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;

    public $handler;

    public $validMarkup = array(
        'bold' => '[b]Bold[/b]
        This line should not be broke as new line'
    );

    public function setUp()
    {
        $bbCode = \Zend_Markup::factory('BbCode', 'Html');
        $bbCode->addTag('br', \Zend_Markup::REPLACE_SINGLE,
            array('replace' => '<br />'));
        $this->handler = new Markup($bbCode);

//        $this->validMarkup = new \Zend_Config_Ini(dirname(__FILE__) .
//            '/../_files/validMarkup.ini', 'production', true);
    }

    /**
     * @desc Asserts that every node that uses markups be replaced
     * with their proper text
     */
    public function testHandle()
    {
//        $this->markTestSkipped(); //until factory config constructor works
        $config = $this->handler->handle(new \Zend_Config($this->validMarkup, array(
            'allowModifications' => true)));

        \Zend_Debug::dump($config);

        $this->assertSame("<strong>Bold</strong>
            This line should not be broke as new line",
            $config->bold);
    }
}

MarkupTest::runAlone();
