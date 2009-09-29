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
    
    protected $_configSectionName;
    
    /**
     * @desc Handles the configuration by finding the import URLs and then merging everything.
     * @param Zend_Config $config
     * @return Zend_Config
     */
    public function handle(\Zend_Config $config, Composite $compositeHandler = null)
    {
        $this->clear();
        $this->_compositeHandler = $compositeHandler;
        $this->_configSectionName = $config->getSectionName();
        
        if( isset( $config->import ) ) {
            $this->setConfigType($config);
            
            if( null !== ($compositeHandler = $this->getCompositeHandler()) ) {
                $config = $compositeHandler->handle($config);
            }
            
            $this->_lookForImports($config->import);
            $this->_mergeAllImports($config);
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
    protected function _lookForImports(\Zend_Config $config)
    {
        foreach( $config as $key => $value ) {
            $this->_importUrls[$key]['parent'] = $value;
            $this->_lookForMoreImports($value, $key);
        }
    }
    
    /**
     * @desc Returns the import URLs
     * @return array The import URLs
     */
    public function getUrls($importUrls)
    {
        $imports = array();
        foreach ($importUrls as $urlSet) {
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
    protected function _mergeAllImports(\Zend_Config $config)
    {
        $imports = $this->getUrls($this->_importUrls);
        foreach($imports as $import) {
            $importing = $this->_getConfigFileByPath($import);
            if( null !== ($compositeHandler = $this->getCompositeHandler()) ) {
                $importing = $compositeHandler->handle($importing);
            }
            $config->merge($importing);
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
    protected function _getConfigFileByPath($configPath)
    {
        try {
            $type           = $this->getConfigType();
            $isZendConfig   = 'Zend_Config' === $type;
            
            if( $isZendConfig && is_string($configPath) ) {
                throw new Exception("Cannot instanciate a Zend_Config with a string");
            }
            
            $config = $isZendConfig 
                    ? new $type($configPath, true) 
                    : new $type($configPath, $this->_configSectionName, true);
        } catch (\Zend_Config_Exception $e) {
            throw new Exception("Cannot instanciate {$type} with path {$configPath}.");
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
    protected function _lookForMoreImports($configPath, $parentKey)
    {
        $imports = $this->getUrls($this->_importUrls);
        
        $examinedConfig = $this->_getConfigFileByPath($configPath);
        
        if( isset( $examinedConfig->import ) ) {
            
            if( null !== $compositeHandler = $this->getCompositeHandler() ) {
                $examinedConfig = $compositeHandler->handle($examinedConfig);
            }
            
            foreach ($examinedConfig->import as $url) {
                if( !in_array($url, $imports) ) {
                    $this->_importUrls[$parentKey]["children"][] = $url;
                    $this->_lookForMoreImports($url, $parentKey);
                }
            }
        }
    }
    
    /**
     * 
     * @param $config The 
     * @return unknown_type
     */
    public function setConfigType(\Zend_Config $config)
    {
        $this->_configType = get_class($config);
    }
    
    public function getConfigType()
    {
        return $this->_configType;
    }
    
    public function getCompositeHandler()
    {
        return $this->_compositeHandler;
    }
    
    public function getImportsHierarchy()
    {
        return $this->_importUrls;
    }
}