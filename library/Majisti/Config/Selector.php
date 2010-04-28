<?php

namespace Majisti\Config;

class Selector
{
    protected $_config;
    
    public function __construct(\Zend_Config $config = null)
    {
        $this->_config = $config;
    }
    
    public function find($selector, $returnDefault = null)
    {
        $config = $this->getConfig();
        $parts = explode('.' , (string)$selector);
        
        $currentSelection = '';
        
        foreach ($parts as $part) {
            $currentSelection .= $part . '.';
        	if( !isset($config->$part)) {
        	    if( null === $returnDefault ) {
        	        rtrim($currentSelection, '.');
            	    throw new Exception("Cannot find current selection
                        [{$currentSelection}]");
        	    }
        	    return $returnDefault;
        	}
        	$config = $config->$part;
        }
        
        return $config;
    }    
    
    public function getConfig()
    {
        return $this->_config;
    }
}
