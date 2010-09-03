<?php

namespace Majisti\Controller\ActionHelper;

require_once 'TestHelper.php';
include_once 'Majisti/Controller/ActionHelper/Model.php';

/**
 * @desc Test class for Controller action helper model class.
 * @author Majisti
 */
class ModelTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var Majisti_Controller_ActionHelper_Model
     */
    protected $_modelContainerActual;

    /**
     * @var \Majisti\Model\Container
     */
    protected $_modelContainerExpected;

    public function setUp()
    {
        $this->_modelContainerExpected  = new \Majisti\Model\Container();
        $this->_modelContainerActual    
            = new \Majisti_Controller_ActionHelper_Model();
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
