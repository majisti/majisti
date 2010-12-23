<?php

namespace Majisti\Util\Model\Aggregator;

require_once __DIR__ . '/TestHelper.php';

/**
 * @desc Test the config class.
 * @author Majisti
 */
class ConfigTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var \Config
     */
    public $aggregator;

    /**
     * @var \Zend_Config
     */
    public $config;

    /**
     * @desc Setups the test case
     */
    public function setUp()
    {
        $this->config       = new \Zend_Config(array('foo' => 'bar'));
        $this->aggregator   = new Config();
    }

    /**
     * Asserts accessors and mutators
     * @expectedException Exception
     */
    public function testGetSetConfig()
    {
        $aggregator = $this->aggregator;
        $config     = $this->config;

        $aggregator->setConfig($config);
        $this->assertEquals($config, $aggregator->getConfig());

        /* throws exception */
        $aggregator->setConfig(array());
    }
}

ConfigTest::runAlone();
