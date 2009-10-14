<?php

namespace MajistiX\Model\Editing;

class Factory
{
    static protected function _configToZendConfig($config)
    {
        if( !($config instanceof \Zend_Config) && is_array($config) ) {
            $config = new \Zend_Config($config);
        }
        
        return $config;
    }
    
    static public function createInPlaceEditingModel($config = array())
    {
        return new \MajistiX\Model\Editing\InPlace(
            static::createStorage($config), 
            static::createEditor($config)
        );
    }
    
    static public function createStorage($config = array())
    {
        $configSelector = new \Majisti\Config\Selector(static::_configToZendConfig($config));
        
        /* find config and in case that a selection is not found, use a default value */
        $storageAdapter = $configSelector->find('plugins.inPlaceEditing.storage.adapter', 'db');
        $storageParams  = $configSelector->find('plugins.inPlaceEditing.storage.params', 
            new \Zend_Config(array()));
            
        $className = __NAMESPACE__ . '\InPlace' . ucfirst((string)$storageAdapter) . 'Storage';
        
        if( !class_exists($className) ) {
            throw new Exception("Adapter {$className} not found");
        }
        
        return new $className($storageParams->toArray());
    }
    
    static public function createEditor($config = array())
    {
        //TODO: complete factory method stub
        return new CkEditor($config);
    }
}
