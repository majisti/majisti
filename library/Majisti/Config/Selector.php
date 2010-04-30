<?php

namespace Majisti\Config;

/**
 * @desc The config selector is a configuration finder that will
 * find anything needed with a CSS like selection.
 *
 * E.g Finding foo.bar will return baz in array('foo' => array('bar' => 'baz'))
 * @author Majisti
 * @license
 */
class Selector
{
    /**
     * @var \Zend_Config
     */
    protected $_config;

    /**
     * @desc Constructs a selector with the given config.
     * @param \Zend_Config $config The config
     */
    public function __construct(\Zend_Config $config = null)
    {
        $this->_config = $config;
    }

    /**
     * @desc Finds a value within the config with the CSS like attribute
     * selector. If a default return value is specified, that value will be
     * returned when the value is not found with the specified selector.
     * If not, an exception is thrown.
     *
     * @param string $selector The CSS like selector (e.g foo.bar.baz)
     * @param string [optionnal] $returnDefault The default return value,
     * keeping null will throw an exception when the value is not found
     * with the specified selector.
     *
     * @throws \Majisti\Config\Exception If the value could not be found
     * with the specified selector and that no default return value was given.
     *
     * @return string The found value
     */
    public function find($selector, $returnDefault = null)
    {
        $config             = $this->getConfig();
        $parts              = explode('.' , (string)$selector);
        $currentSelection   = '';

        /* recursively search and return default value if not found and not null */
        foreach ($parts as $part) {
            $currentSelection .= $part . '.';
        	if( !isset($config->$part)) {
        	    if( null === $returnDefault ) {
        	        $formattedSelection = rtrim($currentSelection, '.');

            	    throw new Exception("Cannot find current selection
                        [{$formattedSelection}]");
        	    }
        	    return $returnDefault;
        	}

        	$config = $config->$part;
        }
        
        return $config;
    }    

    /**
     * @desc Returns the config.
     * @return \Zend_Config the config
     */
    public function getConfig()
    {
        return $this->_config;
    }
}
