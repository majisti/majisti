<?php

namespace Majisit\Util\Model\Aggregator;

class Config implements IConfig
{
    /**
     * @var \Zend_Config
     */
    protected $_config;
    
    public function getConfig()
    {
        return $this->_config;
    }
    
    public function setConfig(\Zend_Config $config)
    {
        $this->_config = $config;
    }
}