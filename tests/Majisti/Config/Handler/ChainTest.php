<?php

namespace Majisti\Config\Handler;

require_once __DIR__ . '/TestHelper.php';

/**
 * @desc Chain test case
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class ChainTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;
    
    /**
     * @var Property
     */
    public $propertyHandler;

    /**
     * @var Import
     */
    public $importHandler;

    /**
     * @var Chain
     */
    public $handler;

    /**
     * Setups the test case
     */
    public function setUp()
    {
        $this->propertyHandler = new Property();
        $this->importHandler   = new Import();
        $this->handler         = new Chain();
    }

    /**
     * @desc Tests the constructor
     */
    public function test__construct()
    {
        $handler = $this->handler;
        $handler->push($this->propertyHandler);
        
        $this->assertNotNull($handler);
        $this->assertContains($this->propertyHandler, $handler,
                'Chain does not contain first element pushed.');
       
        $handler->push($this->importHandler);
        $this->assertEquals(2, $handler->count());
        $this->assertEquals($this->importHandler, $handler->peek(),
                'Chain does not contain second element pushed as top of
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
        $class      = '\Majisti\Config\Handler\Property';
        $handlers   = $this->handler;

        for ($i = 0; $i < 4; $i++) {
            $handlers->push($this->getMock($class));
        }

        $config = new \Zend_Config(array());

        foreach ($handlers as $handler) {
            $handler->expects($this->once())
                    ->method('handle')
                    ->with($config)
                    ->will($this->returnArgument(0));
        }

        $handlers->handle($config);

        $handlers->clear();
    }

    /**
     * @desc Tests exception thrown in contructor
     * @expectedException Exception
     */
    public function testException()
    {
        $invalidHandler = 'foo';
        $handler = $this->handler;
        $this->assertEquals(0, $handler->count());

        $handler->push($invalidHandler);
        $handler->handle(new \Zend_Config(array()));

    }
}

ChainTest::runAlone();
