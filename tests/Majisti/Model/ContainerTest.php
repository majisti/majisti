<?php

namespace Majisti\Model;

require_once __DIR__ . '/TestHelper.php';

/**
 * @desc Asserts that the model container aggregates models correctly.
 * It must provide lazy loading, single models, multiple models instanciation
 * object params, removal and etc.
 * 
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class ContainerTest extends \Majisti\Test\TestCase
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
        $this->container              = new Container();
        $this->mockModel              = new MockModel();
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

        $this->assertTrue($container->hasModel('model1', 'majisti'));
        $this->assertTrue($container->hasModel('model1', 'maJiSti'));
        $this->assertTrue($container->hasModel('moDel1', 'maJiSti'));
        $this->assertFalse($container->hasModel('model1'));
    }
    
    public function testGetModelOnInsertedClass()
    {
        $container = $this->container;
        
        $container->addModel('model1', $this->mockModelClass);
        
        $model = $container->getModel('model1');
        $this->assertEquals(new MockModel(), $model);
        $this->assertTrue($container->hasModel('model1'));
        
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
     * @expectedException \Exception
     */
    public function testGetModelWithObjectParams()
    {
        $container = $this->container;
        
        $container->addModel('textfield', 'Zend_Form_Element_Text', array('tf'));
        $this->assertTrue($container->hasModel('textfield'));
        
        /* object must instanciate with given params */
        $tf = $container->getModel('textfield');
        $this->assertEquals('tf', $tf->getName());
        
        /* 
         * object must be returned without having to specify the params
         * since it is already instanciated
         */
        $tf = $container->getModel('textfield');
        $this->assertEquals('tf', $tf->getName());

        /* object must not instanciate on wrong params */
        $container->addModel('textfield', 'Zend_Form_Element_Text');
        $container->getModel('textfield'); //throws exception
    }
    
    public function testRemoveModel()
    {
        $container = $this->container;
        
        $container->addModel('model1', $this->mockModel);
        $container->addModel('model2', $this->mockModel, 'majisti');
        
        /* removing from default namespace */
        $this->assertTrue($container->removeModel('model1'));
        $this->assertNull($container->getModel('model1'));
        $this->assertFalse($container->hasModel('model1'));
        
        /* removing from namespace */
        $container->getModel('model2', 'majisti');
        $this->assertTrue($container->removeModel('model2', 'majisti'));
        $this->assertNull($container->getModel('model2', 'majisti'));
        $this->assertFalse($container->hasModel('model2', 'majisti'));
        
        /* non existant models or namespaces */
        $this->assertFalse($container->removeModel('nonExistantModel'));
        $this->assertFalse($container->removeModel('nonExistantModel', 'majisti'));
        $this->assertFalse($container->removeModel(
            'nonExistantModel',
            'nonExistantNamespace'
        ));

        $this->assertFalse($container->hasModel('nonExistantModel'));
        $this->assertFalse($container->hasModel(
            'nonExistantModel',
            'nonExistantNamespace'
        ));
    }
}

/**
 * @desc MockModel class used in the assertions
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
