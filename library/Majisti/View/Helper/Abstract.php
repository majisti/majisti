<?php

//namespace Majisti\View\Helper;

abstract class Majisti_View_Helper_Abstract extends \Zend_View_Helper_Abstract
    implements \Majisti\Util\Model\Aggregator\IConfig
{
    protected $_config;

    public function getConfig()
    {
        return Zend_Registry::get('Majisti_Config');
    }

    public function setConfig(Zend_Config $config)
    {
        //TODO: complete method stub
    }
}