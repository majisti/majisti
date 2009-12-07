<?php

namespace Majisti\Model;

/**
 * @desc Container for holding single models by providing namespace access 
 * and lazy instanciation.
 * 
 * @author Steven Rosato
 */
class Container
{
    /**
     * @var \ArrayObject
     */
    protected $_registry;
    
    /**
     * @desc Constructs the model container
     */
    public function __construct()
    {
        $this->_registry = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
    }
    
    /**
     * @desc Adds a model to the container, inside a namespace. Either an object
     * or classname can be provided, autoloaders and pluginloaders should be
     * able to load that class when it is instanciated, in other words,
     * when getModel() is called for the first time on the model key.
     * When no namespace is given, it is stored under the 'default' namespace
     * key. 
     * 
     * @param $key The key for accessing the model
     * @param $model The model, either an object or classname
     * @param $namespace [opt; def=default] The namespace key
     * @param $args [optionnal] The object arguments needed for instanciation
     */
    public function addModel($key, $model, $namespace = 'default', array $args = array())
    {
        /* prepare params */
        if( is_array($namespace) ) {
            $args       = $namespace;
            $namespace  = 'default';
        }
        
        if( null === $namespace ) {
            $namespace = 'default';
        }
        
        $registry   = $this->_registry;
        $namespace  = strtolower((string) $namespace);
        
        /* create namespace */
        if( !$registry->offsetExists($namespace) ) {
            $registry->$namespace = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
        }
        
        /* add model */
        $registry->$namespace->$key = new \ArrayObject(array(
            'model' => $model, 
            'args'  => $args
        ), \ArrayObject::ARRAY_AS_PROPS);
    }
    
    /**
     * @desc Removes a model from the provided key and namespace.
     * 
     * @param $key The key the model was stored in
     * @param $namespace [opt; def=default] The namespace key
     */
    public function removeModel($key, $namespace = 'default')
    {
        $registry   = $this->_registry;
        $namespace  = strtolower((string) $namespace);
        
        /* wrong namespace */
        if( !$registry->offsetExists($namespace) ) {
            return false;
        }
        
        /* wrong key */
        if( !$registry->$namespace->offsetExists($key) ) {
            return false;
        }
        
        /* remove model */
        unset($registry->$namespace->$key);
        
        return true;
    }
    
    /**
     * @desc Retrieves a model from the container. If the model was a classname,
     * instanciation will occur with the args provided with addModel(), 
     * it will be instanciated only once and the object must be accessible through
     * autoloaders and pluginsloaders and errors associated are not handled. Once
     * the instanciation has occured, further calls on getModel() will return
     * that same model.
     * 
     * @param $key The model key stored in this container
     * @param $namespace [opt; def=default] The container's namespace
     */
    public function getModel($key, $namespace = 'default')
    {
        $registry   = $this->_registry;
        $namespace  = strtolower((string) $namespace);
        
        /* namespace not existant, so the model is not found */
        if( !$registry->offsetExists($namespace) ) {
            return null;
        }
        
        /*
         * retrieve model, if it is a class name, attempt to
         * instanciate it with provided args. Once loaded,
         * the container will always return that model.
         */
        if( $registry->$namespace->offsetExists($key) ) {
            $model = $registry->$namespace->$key->model;
            
            if( !is_object($model) ) {
                $model = new \ReflectionClass($model);
                $model = $model->newInstanceArgs($registry->$namespace->$key->args);
                
                $registry->$namespace->$key->model = $model;
                unset($registry->$namespace->$key->args);
            }
            
            return $model;
        }
        
        /* model was not found */
        return null;
    }
}