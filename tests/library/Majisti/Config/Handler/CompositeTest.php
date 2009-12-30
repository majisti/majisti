<?php

namespace Majisti\Config\Handler;

require_once 'TestHelper.php';

/**
 * @desc
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class CompositeTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;
    
    /**
     * @var Composite
     */
    protected $_handler;
    /**
     * Seupts the test case
     */
    public function setUp()
    {
        $this->_handler = new Composite();
    }
    
    public function test__construct()
    {
        $handler = $this->_handler;
        
        $handler->pop();
        $this->markTestIncomplete();
    }
}

CompositeTest::runAlone();
