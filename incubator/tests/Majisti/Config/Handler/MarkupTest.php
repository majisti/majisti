<?php

namespace Majisti\Config\Handler;

require_once 'TestHelper.php';

class MarkupTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;
    
    private $_handler;
    
    private $_validMarkup;
    
    public function setUp()
    {
        $bbCode = \Zend_Markup::factory('BbCode', 'Html');
        $bbCode->addTag('br', \Zend_Markup::REPLACE_SINGLE, 
            array('replace' => '<br />'));
        $this->_handler = new Markup($bbCode);
        
        $this->_validMarkup = new \Zend_Config_Ini(dirname(__FILE__) . 
            '/../_files/validMarkup.ini', 'production', true);
    }
    
    /**
    * @desc Asserts that every node that uses markups be replaced
    * with their proper text
    */
    public function testHandle()
    {
        $this->markTestSkipped(); //until factory config constructor works
        $config = $this->_handler->handle($this->_validMarkup);
        
        $this->assertSame("<br />Start break line", $config->content->br->start);
        $this->assertSame('<span style="text-decoration: underline;">Underlined</span> text', 
            $config->content->underline);
    }
}

MarkupTest::runAlone();
