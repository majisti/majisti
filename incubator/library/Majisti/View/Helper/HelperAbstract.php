<?php

namespace Majisti\View\Helper;

abstract class HelperAbstract extends \Zend_View_Helper_Abstract 
    implements \Majisti\Util\Model\IConfigAggregate
{
    protected $_config;
    
    public function getConfig()
    {
        return \Zend_Registry::get('Majisti_Config');
    }
    
    public function setConfig(\Zend_Config $config)
    {
        
    }
}