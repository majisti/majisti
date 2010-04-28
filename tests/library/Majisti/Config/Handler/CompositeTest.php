<?php

namespace Majisti\Config\Handler;

require_once 'TestHelper.php';

/**
 * @desc Composite test case
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
     * Setups the test case
     */
    public function setUp()
    {
        $this->_propertyHandler = new Property();
        $this->_importHandler   = new Import();
        $this->_handler = new Composite();
    }

    /**
     * @desc Tests the constructor
     */
    public function test__construct()
    {
        $handler = $this->_handler;
        $handler->push($this->_propertyHandler);
        
        $this->assertNotNull($handler);
        $this->assertContains($this->_propertyHandler, $handler, 
                'Composite does not contain first element pushed.');
       
        $handler->push($this->_importHandler);
        $this->assertEquals(2, $handler->count());
        $this->assertEquals($this->_importHandler, $handler->peek(),
                'Composite does not contain second element pushed as top of
                 the stack.');

        $import = $handler->pop();
        $this->assertNotNull($import);
        $this->assertTrue($import instanceof \Majisti\Config\Handler\Import);
        $this->assertEquals(1, $handler->count());
    }

    /**
     * @desc Tests the handle composite function
     */
    public function testHandle()
    {
        $mockA = new Mock();
        $mockB = new Mock();
        $mockC = new Mock();
        $mockD = new Mock();

        $handler = $this->_handler;
        $handler->push($mockA);
        $handler->push($mockB);
        $handler->push($mockC);
        $handler->push($mockD);

        $handler->handle(new \Zend_Config(array()));

        $this->assertTrue($mockA->hasBeenHandled());
        $this->assertTrue($mockB->hasBeenHandled());
        $this->assertTrue($mockC->hasBeenHandled());
        $this->assertTrue($mockD->hasBeenHandled());

        $handler->clear();
    }

    /**
     * @desc Tests exception thrown in contructor
     * @expectedException Exception
     */
    public function testException()
    {
        $invalidHandler = 'foo';
        $handler = $this->_handler;
        $this->assertEquals(0, $handler->count());

        $handler->push($invalidHandler);
        $handler->handle(new \Zend_Config(array()));

    }
}

CompositeTest::runAlone();
