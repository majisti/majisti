<?php

namespace MajistiX\Extensions\InPlaceEditing\Model;

/**
 * @desc Factory class to help create an InPlaceEditing model from configuration.
 * 
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Factory
{
    /**
     * @desc Transform a config array to a \Zend_Config object.
     * 
     * @param \Zend_Config | array $config The configuration data
     * 
     * @return \Zend_Config the config object
     */
    static protected function configToZendConfig($config)
    {
        if( !($config instanceof \Zend_Config) && is_array($config) ) {
            $config = new \Zend_Config($config);
        }
        
        return $config;
    }
    
    /**
     * @desc Creates an InPlaceEditing model with an editor and storage model
     * according to configuration.
     * 
     * @param \Zend_Config | array $config The InPlaceEditing configuration
     * 
     * @return InPlaceEditing The created InPlaceEditing model
     */
    static public function createInPlaceEditingModel($config = array())
    {
        return new \MajistiX\Extensions\InPlaceEditing\Model\InPlaceEditing(
            static::createStorage($config), 
            static::createEditor($config)
        );
    }
    
    /**
     * @desc Creates a storage model to use with the InPlaceEditing model
     * according to the provided configuration data.
     * 
     * @param \Zend_Config | array $config The InPlaceEditing configuration data
     * 
     * @return Storage\IStorage The created storage model
     */
    static public function createStorage($config = array())
    {
        $configSelector = new \Majisti\Config\Selector(static::_configToZendConfig($config));
        
        /* find config and in case that a selection is not found, use a default value */
        $storageAdapter = $configSelector->find('resources.extensions.InPlaceEditing.storage.adapter', 'db');
        $storageParams  = $configSelector->find('resources.extensions.InPlaceEditing.storage.params', 
            new \Zend_Config(array()));

        /* instanciate storage model and return it */
        $className = __NAMESPACE__ . '\Storage\\' . ucfirst((string)$storageAdapter);
        
        if( !class_exists($className) ) {
            throw new Exception("Adapter {$className} not found.");
        }
        
        return new $className($storageParams->toArray());
    }
    
    /**
     * @desc Creates an editor to use with the InPlaceEditing model according
     * to the provided configuration data.
     * 
     * @param \Zend_Config | array $config The InPlaceEditing configuration data.
     * 
     * @return Editor\IEditor The created editor.
     */
    static public function createEditor($config = array())
    {
        //TODO: complete factory method stub
        return new Editor\CkEditor($config);
    }
}
