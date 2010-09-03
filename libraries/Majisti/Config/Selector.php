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
     * @desc Serves as a void argument when the returnConfigAsArray is used
     * but an exception still wants to be thrown
     */
    const VOID = 'MAJISTI_CONFIG_SELECTOR_VOID';

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
     * @param string $selection The CSS like selector (e.g foo.bar.baz)
     * @param string [opt; def=Selector::VOID] $returnDefault The default
     * return value, keeping null will throw an exception when the value
     * is not found with the specified selector.
     * @param bool $returnConfigAsArray [opt; def=false] Return the parent
     * element as an array instead of a \Zend_Config.
     *
     * @throws \Majisti\Config\Exception If the value could not be found
     * with the specified selector and that no default return value was given.
     *
     * @return string The found value
     */
    public function find($selection, $returnDefault = self::VOID,
        $returnConfigAsArray = false)
    {
        $config             = $this->getConfig();
        $parts              = explode('.' , (string)$selection);
        $currentSelection   = '';

        /* recursively search and return default value if not found and not null */
        foreach ($parts as $part) {
            $currentSelection .= $part . '.';
        	if( !isset($config->$part)) {
                if( $returnDefault === self::VOID ) {
        	        $formattedSelection = rtrim($currentSelection, '.');

            	    throw new Exception("Cannot find current selection
                        [{$formattedSelection}]");
        	    }

        	    return $returnDefault;
        	}

        	$config = $config->$part;
        }

        if( $returnConfigAsArray && $config instanceof \Zend_Config ) {
            $config = $config->toArray();
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
