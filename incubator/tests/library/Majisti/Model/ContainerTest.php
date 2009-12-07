<?php

namespace Majisti\Model;

require_once 'TestHelper.php';

/**
 * @desc Asserts that the model container aggregates models correctly.
 * It must provide lazy loading, single models, multiple models instanciation
 * object params, removal and etc.
 * 
 * @author Steven Rosato 
 */
class ContainerTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;
    
    /**
     * @var Container
     */
    public $container;
    
    /**
     * @var MockModel
     */
    public $mockModel;
    
    /**
     * @var string
     */
    public $mockModelClass = '\Majisti\Model\MockModel';
    
    /**
     * @desc Setups the test case
     */
    public function setUp()
    {
        $this->container = new Container();
        $this->mockModel = new MockModel();
    }
    
    public function testAddModelOverridesExistingModel()
    {
        $model      = $this->mockModel;
        $container  = $this->container;
        
        $model->foo = 'foo';
        $container->addModel('model1', $model);
        
        /* create new model to insert at same key */
        $newModel = new MockModel(array('bar' => 'bar'));
        $container->addModel('model1', $newModel);
        
        /* mockModel should have been replaced by newModel */
        $this->assertNotEquals($model, $container->getModel('model1'));
        $this->assertEquals($newModel, $container->getModel('model1'));
        
        /* test with namespaces too, case insensitive */
        $container->addModel('model1', $model, 'majisti');
        $container->addModel('model1', $newModel, 'MaJiSti');
        
        $this->assertEquals($newModel, $container->getModel('model1', 'majisti'));
    }
    
    public function testAddModelAddsToCorrectNamespaces()
    {
        $model      = $this->mockModel;
        $container  = $this->container;
        
        $container->addModel('model1', $model, 'majisti');
        
        /* model should be available on majisti namespace, case insensitive */
        $this->assertNull($container->getModel('model1')); //default namespace
        $this->assertEquals($model, $container->getModel('model1', 'majisti'));
        $this->assertEquals($model, $container->getModel('model1', 'MaJiSti'));
    }
    
    public function testGetModelOnInsertedClass()
    {
        $container = $this->container;
        
        $container->addModel('model1', $this->mockModelClass);
        
        $model = $container->getModel('model1');
        $this->assertEquals(new MockModel(), $model);
        
        $model->foo = 'foo';
        
        /* it should not instanciate a new model but instead return the same */
        $this->assertEquals($model, $container->getModel('model1'));
    }
    
    public function testGetIncorrectModelReturnsNull()
    {
        $container = $this->container;
        
        /* non existant model on existant namespace */
        $this->assertNull($container->getModel('nonExistantModel'));
        
        $container->addModel('model1', $this->mockModel, 'majisti');
        
        /* existant model on non existant namespace */
        $this->assertNull($container->getModel('model1', 'majistix'));
    }
    
    /**
     * 
     */
    public function testGetModelWithObjectParams()
    {
        $container = $this->container;
        $container->addModel('textfield', 'Zend_Form_Element_Text');
        
        /* object must not instanciate on wrong params */
        try {
            $container->getModel('textfield');
            $this->fail('Should throw exception since no arguments provided');
        } catch( \Exception $e ) {
            
        }
        
        $container->addModel('textfield', 'Zend_Form_Element_Text', array('tf'));
        
        /* object must instanciate with given params */
        $tf = $container->getModel('textfield');
        $this->assertEquals('tf', $tf->getName());
        
        /* 
         * object must be returned without having to specify the params
         * since it is already instanciated
         */
        $tf = $container->getModel('textfield');
        $this->assertEquals('tf', $tf->getName());
    }
    
    public function testRemoveModel()
    {
        $container = $this->container;
        
        $container->addModel('model1', $this->mockModel);
        $container->addModel('model2', $this->mockModel, 'majisti');
        
        /* removing from default namespace */
        $this->assertTrue($container->removeModel('model1'));
        $this->assertNull($container->getModel('model1'));
        
        /* removing from namespace */
        $container->getModel('model2', 'majisti');
        $this->assertTrue($container->removeModel('model2', 'majisti'));
        $this->assertNull($container->getModel('model2', 'majisti'));
        
        /* non existant models or namespaces */
        $this->assertFalse($container->removeModel('nonExistantModel'));
        $this->assertFalse($container->removeModel('nonExistantModel', 'majisti'));
        $this->assertFalse($container->removeModel('nonExistantModel', 'nonExistantNamespace'));
    }
}

/**
 * @desc MockModel class used in the assertions
 * 
 * @author Steven Rosato
 */
class MockModel extends \ArrayObject
{
    public function __construct($data = array())
    {
        $dataToInsert = array('default' => 'value');
        
        if( !empty($data) ) {
            $dataToInsert = array_merge($dataToInsert, $data);
        }
        
        parent::__construct($dataToInsert);
    }
}

ContainerTest::runAlone();
