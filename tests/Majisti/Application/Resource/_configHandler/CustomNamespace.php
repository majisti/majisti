<?php

namespace My\Config\Handler;

class CustomNamespace implements \Majisti\Config\Handler\IHandler
{
    public $params;

    public function __construct($params = null)
    {
        $this->params = $params;
    }

    public function handle(\Zend_Config $config)
    {
        return $config;
    }
}
