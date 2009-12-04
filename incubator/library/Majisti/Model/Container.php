<?php

namespace Majisti\Model;

class Container
{
    protected $_models = array();
    
    public function addModel($key, $model, $namespace = 'default')
    {
        if( !array_key_exists($namespace, $this->_models) ) {
            $this->_models[$namespace] = array();
        }
        
        $this->_models[$namespace][$key] = $model;
    }
    
    public function removeModel($key)
    {
        
    }
    
    public function getModel($key, $namespace = 'default')
    {
        if( array_key_exists($key, $this->_models[$namespace]) ) {
            $model = $this->_models[$namespace][$key];
            
            if( !is_object($model) ) {
                $model = new $model();
                $this->_models[$namespace][$key] = $model;
            }
            
            return $model;
        }
        
        throw new Exception("Model '{$key}' does not exists in namespace '{$namespace}'");
    }
}