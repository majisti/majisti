<?php

namespace Majisti\Application\Resource;

require_once 'TestHelper.php';

/**
 * @desc Test for ModelContainer resource
 * @author Majisti
 */
class ModelContainerTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var \Majisti\Application\Resource\ModelContainer
     */
    public $resource;

    /**
     * @var \Majisti\Model\Container
     */
    public $modelContainer;

    /**
     * Setups the test case
     */
    public function setUp()
    {
        $this->resource         = new Modelcontainer();
        $this->modelContainer   = new \Majisti\Model\Container();
    }

    public function testModelContainerGetsRegistered()
    {
        $this->assertEquals($this->modelContainer, $this->resource->init());
    }

    public function testModelContainerDoesNotOverrideAlreadyRegisteredContainer()
    {
        $returnedModelContainer = $this->resource->init();
        $modelContainer = new \Majisti\Model\Container();

        \Zend_Registry::set('Majisti_ModelContainer', $modelContainer);

        $this->assertEquals($returnedModelContainer, $this->resource->init());
    }
}

ModelContainerTest::runAlone();
