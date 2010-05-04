<?php

namespace Majisti\Model\Data;

require_once 'TestHelper.php';

/**
 * @desc
 * @author 
 */
class XmlTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var Xml
     */
    protected $_xml;

    /**
     * @var string
     */
    protected $_xmlPath;

    /**
     * @var bool
     */
    protected $_useBBCodeMarkup;

    /**
     * Setups the test case
     */
    public function setUp()
    {
       $this->_useBBCodeMarkup = true;
       $this->_xmlPath = 'foo';
       $this->_xml = new Xml();
    }

    /**
     * @desc Tests the pushMarkup() behaviour
     */
    public function testPushMarkup()
    {
        
    }
}

XmlTest::runAlone();
