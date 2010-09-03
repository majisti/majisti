<?php

namespace Majisti\Application\Resource;

require_once 'TestHelper.php';

/* additionnal classes needed for testing */
require_once dirname(__FILE__) . '/_configHandler/Custom.php';
require_once dirname(__FILE__) . '/_configHandler/CustomNamespace.php';

/**
 * @desc ConfighandlerTest asserts that the Confighandler class
 * can push handlers to a composite handler correctly and handle
 * them afterwards.
 *
 * @author Majisti
 */
class ConfighandlerTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var \Zend_Config
     */
    public $config;

    /**
     * Will change to Zend_Config on setup.
     * @var \Zend_Config
     */
    public $wrongConfig = array(
        'wrongClassname' => array(
            'custom' => 'Non_Existant_Class'
        ),
        'wrongNamespaceClassname' => array(
            'custom' => '\Non\Existant\Class'
        ),
    );

    /**
     * @var Confighandler
     */
    public $configHandler;

    /**
     * @desc Setups the test case
     */
    public function setUp()
    {
        $this->config = new \Zend_Config_Ini(
            dirname(__FILE__) . '/_configHandler/config.ini',
            'production',
            array('allowModifications' => true)
        );
        \Zend_Registry::set('Majisti_Config', $this->config);

        $this->configHandler = new Confighandler(
            $this->config->resources->configHandler);
            
        $this->wrongConfig = new \Zend_Config($this->wrongConfig);
    }

    /**
     * @desc Assert that all handlers get instanciated properly
     * in the Composite handler.
     */
    public function testInitWithProperConfig()
    {
        $confHandler = $this->configHandler;
        $composite   = $confHandler->init();

        /* assert custom namespace with arguments */
        $customWithArgs = $composite->pop();
        $this->assertType('My\Config\Handler\CustomNamespace', $customWithArgs);
        $this->assertEquals(
            array('key' => 'value1', 'value2'),
            $customWithArgs->params
        );

        /* assert all other handlers are of the correct types */
        $this->assertType('My\Config\Handler\CustomNamespace', $composite->pop());
        $this->assertType('My_Config_Handler_Custom', $composite->pop());
        $this->assertType('Majisti\Config\Handler\Import', $composite->pop());

        $this->assertTrue($composite->isEmpty());

        /* calling init again will return same composite */
        $this->assertSame($composite, $confHandler->init());
    }

    /**
     * @desc Initial composite handler should be empty
     */
    public function testInitialCompositeIsEmpty()
    {
        $this->assertTrue($this->configHandler->getCompositeHandler()->isEmpty());
    }

    /**
     * @desc Asserts that wrong configuration throws exceptions
     */
    public function testWrongConfigThrowsException()
    {
        $init = function($handler) {
            try {
                $handler->init();
                $this->fail('Should throw exception');
            } catch( \Exception $e ) {}
        };
        
        foreach ($this->wrongConfig as $config) {
        	$init(new Confighandler($config));
        }
    }
}

ConfighandlerTest::runAlone();