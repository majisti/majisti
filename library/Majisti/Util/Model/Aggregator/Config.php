<?php

namespace Majisti\Util\Model\Aggregator;

/**
 * @desc The config aggregator is simply a model that supports configuration.
 * Model extending it must make use of a configuration mechanism in their
 * implementation
 *
 * @author Majisti
 */
class Config implements IConfig
{
    /**
     * @var \Zend_Config
     */
    protected $_config;

    /**
     * @desc Returns the config object
     * @return \Zend_Config The config object
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * @desc Sets the config object
     * @param Zend_Config $config The config object
     */
    public function setConfig(\Zend_Config $config)
    {
        $this->_config = $config;
    }
}