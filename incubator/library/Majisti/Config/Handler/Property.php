<?php

namespace Majisti\Config\Handler;

/**
 * @desc Property Handler based on Apache Ant to load a properties scope
 * from a configuration. Properties follow the apache id/value paired
 * properties and as soon as a property is declared, it is possible
 * to call it on any values. Note that it is not supported on keys.
 * 
 * @see http://en.wikibooks.org/wiki/Apache_Ant/Property
 * @author Steven Rosato
 */
class Property implements IHandler
{
    /**
     * @desc Properties bag
     * @var array
     */
    protected $_properties = array();
    
    /**
     * @desc Needed for stack tracing when calling a property fails
     * @var array
     */
    protected $_passedKeys;
    
    /**
     * @desc Syntax used for calling defined properties. 'id' serves as the 
     * delimiter.
     * @var string
     */
    protected $_syntax = '#{id}';
    
    /**
     * @desc Handles the configuration by parsing it for properties
     * and replacing the called properties in values afterwards.
     * @param Zend_Config $config
     * @return Zend_Config
     */
    public function handle(\Zend_Config $config)
    {
//        $this->clear();
        if( isset($config->property) ) {
            $this->_loadProperties($config->property);
            $config->merge($this->_parseConfigWithProperties($config, 
                $this->getProperties()));
            unset($config->property);
        }
        
        return $config;
    }
    
    /**
     * @desc Clears the property handler from its properties.
     * @return Property this
     */
    public function clear()
    {
        $this->_properties = array();
        $this->_passedKeys = null;
        
        return $this;
    }
    
    /**
     * @desc Returns a property based on an id.
     * @param $id The property's id
     * @return string|null The property or null if it does not exist
     */
    public function getProperty($id)
    {
        if( array_key_exists($id, $this->_properties) ) {
            return $this->_properties[$id];
        }
        
        return null;
    }
    
    /**
     * @desc Returns the properties as an array.
     * @return array The properties
     */
    public function getProperties()
    {
        return $this->_properties;
    }
    
    /**
     * @desc Sets the properties.
     * @param array $properties The properties
     */
    protected function _setProperties(array $properties)
    {
        $this->_properties = $properties;
    }
    
    /**
     * @desc Returns whether the handler has properties loaded.
     * @return bool True if the handler has properties
     */
    public function hasProperties()
    {
        return count($this->_properties) > 0;
    }
    
    /**
     * @desc Returns the syntax used for calling declared properties.
     * @return string The syntax
     */
    public function getSyntax()
    {
        return $this->_syntax;
    }
    
    /**
     * @desc Sets the syntax for calling defined properties
     * @param $prefix
     * @param $suffix
     * @return Property this
     */
    public function setSyntax($prefix, $suffix)
    {
        $this->_syntax = (string) $prefix . 'id' . (string) $suffix;
        
        return $this;
    }
    
    /**
     * @desc Fetches all the declared properties in the Majisti's properties
     * section, and stores them in this configuration's properties bag
     * and replaces the found keys' values inside the config provided
     *
     * @param \Zend_Config $properties A \Zend_Config loaded only with the 
     *  properties
     */
    protected function _loadProperties(\Zend_Config $properties)
    {
        $this->_setProperties($this->_resolveProperties($properties->toArray()));
    }
    
    /**
     * @desc Resolves the properties themselves so that any property
     * that calls other properties will replace that property.
     * @param array $properties The properties
     * @return array The parsed (resolved) properties
     */
    protected function _resolveProperties(array $properties)
    {
        $resolvedProperties = array();
        
        foreach ($properties as $key => $value) {
            $matches = $this->_findPropertiesWithinValue($value);
            
            if( count($matches) ) {
                try {
                    $resolvedProperties[$key] = $this->_replaceValueFromProperties(
                        $value, $resolvedProperties);
                } catch (Exception $e) {
                    throw new Exception("Tried to replace value [{$value}]
                        within Majisti's properties scope on property [{$key}]
                        but no property [{$matches[1][0]}] was declared prior to it");
                }
            } else {
                $resolvedProperties[$key] = $value;
            }
        }
        
        return $resolvedProperties;
    }
    
    /**
     * @desc Parses the given configuration with the given properties,
     * replacing every key found in the configuration
     * data values.
     * 
     * @param array $properties The properties
     * @param \Zend_Config $config The configuration to parse
     * @return \Zend_Config The parsed config
     */
    protected function _parseConfigWithProperties(\Zend_Config $config, array $properties)
    {
        if( empty($this->_passedKeys) ) {
            $this->_passedKeys = array();
        }
        
        foreach ($config as $key => $value) {
            if( $value instanceof \Zend_Config ) {
                $this->_passedKeys[] = $key;
                $value->merge($this->_parseConfigWithProperties($value, 
                    $properties));
            } else {
                try {
                    $config->{$key} = $this->_replaceValueFromProperties($value, 
                        $properties);
                } catch(Exception $e) {
                    $nodeNamespace = implode('.', $this->_passedKeys);
                    $property = $this->_findPropertiesWithinValue($value);
                    throw new Exception("Tried to call non declared property
                        [{$property[1][0]}] on node [{$nodeNamespace}.{$key}]");
                }
            }
        }
        
        return $config;
    }
    
    /**
     * @desc Finds all called properties in a value.
     * @param $value The value to search properties within
     * @return Array The matches following the preg_match returned array
     * except that if there was no matches an empty array is returned
     * (and not an array with two empty arrays)
     */
    protected function _findPropertiesWithinValue($value)
    {
        $syntaxParts = explode('id', $this->getSyntax(), 2);

        $syntaxPreffix  = preg_quote($syntaxParts[0]);
        $syntaxSuffix   = preg_quote($syntaxParts[1]);
        
        preg_match_all("/{$syntaxPreffix}(.*){$syntaxSuffix}/U", $value, $matches);
        
        if( !count($matches[0]) ) {
            $matches = array();
        }
        
        return $matches;
    }
    
    /**
     * @desc Replaces a value based on the given properties
     * @param array $value The value that contains properties
     * @param $properties The properties declared
     * @return The replaced value with the property's value
     */
    protected function _replaceValueFromProperties($value, array $properties)
    {
        $matches = $this->_findPropertiesWithinValue($value);
        
        if( count($matches) ) {
            foreach ($matches[1] as $i => $match) {
                if( !array_key_exists($match, $properties) ) {
                    throw new Exception("The property key [{$match}]
                    was not found");
                }
                $value = str_replace($matches[0][$i], $properties[$match], $value);
            }
        }
        
        return $value;
    }
}
