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
     * @var ModelContainer
     */
    public $container;
    
    /**
     * Setups the test case
     */
    public function setUp()
    {
        $this->container = new Container();
    }
    
    public function testAddModelOverridesExistingModel()
    {
        $this->markTestIncomplete();
    }
    
    public function testAddModelAddsToCorrectNamespaces()
    {
        $this->markTestIncomplete();
    }
    
    public function testGetModelMultipleTimesReturnsSameModel()
    {
        $this->markTestIncomplete();
    }
    
    public function testGetModelReturnsNewModel()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * 
     */
    public function testGetIncorrectModelThrowsException()
    {
        $this->markTestIncomplete();
    }
    
    public function testGetModelWithObjectParams()
    {
        /* object must not instanciate on wrong params */
        
        /* object must instanciate with given params */
        
        /* 
         * object must be returned without having to specify the params
         * since it is already instanciated
         */
        
        $this->markTestIncomplete();
    }
}

ContainerTest::runAlone();
