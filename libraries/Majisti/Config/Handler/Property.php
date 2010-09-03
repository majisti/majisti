<?php

namespace Majisti\Config\Handler;

/**
 * @desc Property Handler based on Apache Ant to load a properties scope
 * from a configuration. Properties follow the apache id/value paired
 * properties and as soon as a property is declared, it is possible
 * to call it on any values. Note that it is not supported on keys.
 *
 * @see http://en.wikibooks.org/wiki/Apache_Ant/Property
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Property implements IHandler
{
    /**
     * @desc Properties bag
     * @var array
     */
    protected $_properties = array();
    
    /**
     * @desc Needed for stack tracing when calling a property throws an
     * exception
     * @var array
     */
    protected $_passedKeys;
    
    /**
     * @desc Syntax used for calling defined properties.
     * @var array Associative array with prefix and postfix
     */
    protected $_syntax = array(
        'prefix'    => '#{',
        'postfix'   => '}'
    );
    
    /**
     * @desc Handles the configuration by parsing it for properties
     * and replacing the called properties in values afterwards.
     *
     * As long as the handler is not cleared, everytime the handle function
     * is called, it will stack up or override prexisting properties
     * and therefore the detected one in the new config will get replaced.
     *
     * @see clear() For clearing the properties
     * @param \Zend_Config $config The config object, it won't be modified
     * @param boolean $clearFirst clear properties before parsing
     * @return \Zend_Config The handled config
     */
    public function handle(\Zend_Config $config, $clearFirst = false)
    {
        $config = new \Zend_Config($config->toArray(), true);

        if( $clearFirst ) {
            $this->clear();
        }
        
        $this->loadProperties($config);
        
        if( $this->hasProperties() ) {
            $config->merge($this->parseConfigWithProperties($config,
                $this->getProperties()));

            $majisti = $config->majisti;
            unset($majisti->property);
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
     *
     * Ex: array('applicationPath' => '/var/www/myProject');
     *
     * @param array $properties The properties as an id => value paired
     * array
     * @param boolean $stackUp [optional] whether to stack up the given
     * properties, same keys will override previous values.
     *
     * @return Property this
     */
    public function setProperties(array $properties, $stackUp = true)
    {
        $this->_properties = $stackUp
            ? array_merge($this->_properties, $properties)
            : $properties;
        
        return $this;
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
     * @return Array Associative array with prefix and postfix keys
     */
    public function getSyntax()
    {
        return $this->_syntax;
    }
    
    /**
     * @desc Sets the syntax for calling defined properties
     * @param string $prefix The prefix
     * @param string $suffix The postfix
     * @return Property this
     */
    public function setSyntax($prefix, $postfix)
    {
        $this->_syntax = array(
            'prefix'    => $prefix,
            'postfix'   => $postfix
        );
        
        return $this;
    }
    
    /**
     * @desc Fetches all the declared properties in the properties
     * section, and stores them in this configuration's properties bag
     * and replaces the found keys' values inside the config provided
     *
     * @param \Zend_Config $properties A \Zend_Config loaded only with the
     * properties
     */
    protected function loadProperties(\Zend_Config $config)
    {
        $properties = $this->getProperties();
        $selector   = new \Majisti\Config\Selector($config);

        if( $props = $selector->find('majisti.property', false) ) {
            $properties = array_merge($properties, $props->toArray());
        }
            
        $this->setProperties($this->resolveProperties($properties));
    }
    
    /**
     * @desc Resolves the properties themselves so that any property
     * that calls other properties will replace that property.
     * @param array $properties The properties
     * @return array The parsed (resolved) properties
     */
    protected function resolveProperties(array $properties)
    {
        $resolvedProperties = array();
        
        foreach ($properties as $key => $value) {
            $matches = $this->findPropertiesWithinValue($value);
            
            if( count($matches) ) {
                try {
                    $resolvedProperties[$key] = $this->replaceValueFromProperties(
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
    protected function parseConfigWithProperties(\Zend_Config $config, array $properties)
    {
        if( empty($this->_passedKeys) ) {
            $this->_passedKeys = array();
        }
        
        foreach ($config as $key => $value) {
            if( $value instanceof \Zend_Config ) {
                $this->_passedKeys[] = $key;
                $value->merge($this->parseConfigWithProperties($value,
                    $properties));
            } else {
                try {
                    $config->{$key} = $this->replaceValueFromProperties($value,
                        $properties);
                } catch(Exception $e) {
                    $nodeNamespace = implode('.', $this->_passedKeys);
                    $property = $this->findPropertiesWithinValue($value);
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
    protected function findPropertiesWithinValue($value)
    {
        $syntax = $this->getSyntax();

        $syntaxPrefix  = preg_quote($syntax['prefix']);
        $syntaxPostfix = preg_quote($syntax['postfix']);
        
        preg_match_all("/{$syntaxPrefix}(.*){$syntaxPostfix}/U", $value, $matches);
        
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
    protected function replaceValueFromProperties($value, array $properties)
    {
        $matches = $this->findPropertiesWithinValue($value);
        
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
