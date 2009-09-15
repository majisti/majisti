<?php

namespace Majisti\Config\Handler;

/**
 * @desc Import handler enabling a configuration file to import 
 * another configuration file and 
 * merging the files into the current \Zend_Config object.
 * The ImportHandler will merge on an ascendant manner, overriding the parent 
 * configuration values, if necessary, 
 * with the children configuration files imported.
 * 
 * Ex:  A core module calling upon the Users module, thus importing it's configuration file, will cause
 *      the core configuration values to be overriden by the Users' configuration values if duplicate
 *      data is found.
 * 
 * The Import Handler digs recursively into the configuration files, meaning that a configuration file
 * can import one or several other files, wich can themselves import and so on.
 * 
 * Note: Circular importing is not supported.
 * 
 * @author Jean-Francois Hamelin
 */
class Import implements IHandler
{
    /*
     * Array containing the URLs to import
     * Parent URL is the configuration file making the import.
     * Children URL is/are the configuration files demanded recursively.
     * Structure goes like this: array[key]=>array['parent']   = 'SomeUrl'
     *                                     =>array['children'] = array['url1'] [...] 
     */
    protected $_importUrls = array();
    
    protected $_configType;
    
    protected $_compositeHandler;
    
    /**
     * @desc Handles the configuration by finding the import URLs and then merging everything.
     * @param Zend_Config $config
     * @return Zend_Config
     */
    public function handle(\Zend_Config $config, $compositeHandler = null)
    {
        $this->clear();
        if( isset( $config->import ) ) {
            $this->_setCallerType($config);
            $this->_findImports($config->import, $resolveProperties);
            $this->_mergeAllImports($config, $resolveProperties);
            unset($config->import);
        }
        return $config;
    }
    
    /**
     * @desc Clears the import handler from its URLs.
     * @return Property this
     */
    public function clear()
    {
        $this->_importUrls = array();
        return $this;
    }
    
    /**
     * @desc Function called on the FIRST PARENT configuration file.
     *       Function will parse the 1st file and look for the first importations.
     *       Then, it will validate the depth of the requested files and dig through them if necessary.
     * @param Zend_Config $config
     * @return void
     */
    protected function _findImports(\Zend_Config $config, $resolveProperties)
    {
        foreach( $config as $key => $value ) {
            $this->_importUrls[$key]['parent'] = $value;
            $this->_validateDepth($value, $key, $resolveProperties);
        }
    }
    
    /**
     * @desc Returns the import URLs as an array.
     * @see The array's structure above at variable declaration.
     * @return array The import URLs
     */
//    public function getImports()
//    {
//        return $this->_importUrls;
//    }
    
    public function getUrls()
    {
        return $this->getFlatArray();
    }
    
    /**
     * @desc Returns the import URLs
     * @deprecated Steven
     * @return array The import URLs
     */
    public function getFlatArray()
    {
        $imports = array();
        foreach ($importsArray as $urlSet) {
        	$imports[] = $urlSet['parent'];
        	if( array_key_exists('children', $urlSet) && is_array($urlSet['children']) ) {
        	    $children = array_values($urlSet['children']);
        	    $imports = array_merge($imports, $children);
        	}
        }
        return $imports;
    }
    
    /**
     * @desc Merging function that iterates through the $_importUrls array and will merge both
     *       the PARENT URLs (1st importations) and the CHILDREN URLs.
     *       
     *       Exemple: Core configuration imports the Forums module's configuration.  Forums will be PARENT.
     *                Forums' configuration relies on the Users' configuration and imports it in his own file.
     *                Users' configuration URL will be CHILDREN.  Any files required by the Users' configuration 
     *                will also be considered children URLs of the parent Forums.
     * @param Zend_Config $config
     * @return void
     */
    protected function _mergeAllImports(\Zend_Config $config, $solveProperties)
    {
        $imports = $this->getFlatArray($this->getImports());
        foreach($imports as $import) {
            $target = $this->buildConfigFile($import);
            if( $solveProperties ) {
                $handler = $this->getPropertyHandler();
                $target = $handler->handle($target);
            }
            $config->merge($target);
        }
    }
    
    /**
     * @desc Attempts to build a \Zend_Config object with the specified path 
     * and catches any config exceptions
     * @param $configPath
     * @param $allowModifications true|false
     * @return Zend_Config
     * 
     */
    protected function _buildConfigFile($configPath, $allowModifications = true)
    {
        $config = new \Zend_Config(array());
        try {
            $type = $this->getCallerType();
            $config = new $type($configPath, null, $allowModifications);
        } catch (\Zend_Config_Exception $e) {
<<<<<<< .mine
//              print "Cannot instanciate {$type} with path {$configPath}.<br />";
//            throw new Exception("Cannot instanciate {$type} with path {$configPath}.");
=======
            throw new Exception("Cannot instanciate {$type} with path {$configPath}.");
>>>>>>> .r401
        }
        
        return $config;
    }
    
    /**
     * @desc Validates the depth of the imports demanded.  If the importation URL does not contain any other
     *       importations, no action is required.  If it does however, recursive digging will occur. 
     * @param $configPath
     * @param $parentKey
     * @return void
     */
    protected function _validateDepth($configPath, $parentKey, $resolveProperties)
    {
        $imports = $this->getFlatArray($this->getImports());
        $tempConfig = $this->buildConfigFile($configPath);
        
        if( $resolveProperties ) {
            $propertyHandler = $this->getPropertyHandler();
            $tempConfig = $propertyHandler->handle($tempConfig);
        }
        
        if( null !== $tempConfig->import ) {
            foreach ($tempConfig->import as $url) {
                if( !in_array($url, $imports) ) {
                    $this->_dig($url, $parentKey);
                }
            }
        }
    }
    
    /**
     * @desc Inserts the children URL found in the import array and digs every URL found
     *       afterward until none is found.
     * @param $url
     * @param $parentKey
     * @return void
     * 
     * FIXME: rename this functioni
     */
    protected function _dig($url, $parentKey)
    {
        $this->_importUrls[$parentKey]["children"][] = $url;
        
        $config = $this->buildConfigFile($url);
        if( $config->import ) {
            foreach($config->import as $url) {
                $this->_dig($url, $parentKey);
            }
        }
    }
    
    protected function _setCallerType(\Zend_Config $config)
    {
        $this->_configType = get_class($config);
    }
    
    /**
     * @deprecated
     * @return unknown_type
     */
    public function getCallerType() 
    {
        return $this->_configType;
    }
    
    
    /**
     * 
     * @param $config The 
     * @return unknown_type
     */
    public function setConfigType(\Zend_Config $config)
    {
        //get_class($config);
    }
    
    public function getConfigType()
    {
        //TODO:
    }
    
    public function getPropertyHandler()
    {
        if( !isset( $this->_propertyHandler ) ) {
            $this->_propertyHandler = new Property();
        }
        
        return $this->_propertyHandler;
    }
}