<?php

namespace Majisti\Controller\ActionHelper;

require_once __DIR__ . '/TestHelper.php';

/**
 * @desc Test class for Controller action helper model class.
 * @author Majisti
 */
class ModelTest extends \Majisti\Test\TestCase
{
    /**
     * @var Model
     */
    protected $_modelContainerActual;

    /**
     * @var \Majisti\Model\Container
     */
    protected $_modelContainerExpected;

    public function setUp()
    {
        //FIXME: class is not using dependancy injection
        \Zend_Registry::set('Majisti_ModelContainer',
            new \Majisti\Model\Container());

        $this->_modelContainerExpected  = new \Majisti\Model\Container();
        $this->_modelContainerActual    = new Model();
    }

    /**
     * Testing that the direct() function returns a model container.
     */
    public function testDirectFunction()
    {
        $this->assertNotNull($this->_modelContainerActual->direct());
        $this->assertEquals($this->_modelContainerExpected,
                $this->_modelContainerActual->direct(),
                'ModelContainer not the same');
    }
}

ModelTest::runAlone();
