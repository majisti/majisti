<?php

namespace Majisti\Controller\Dispatcher;

/**
 * @desc Dispatcher interface that adds fallback controller directories
 * and support PHP namespaces.
 * 
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
interface IDispatcher extends \Zend_Controller_Dispatcher_Interface
{
    /**
     * @desc Adds a fallback controller directory.
     * 
     * @param $path The path to add
     * @param $module [optional, defaults to default module] The module name
     * @return Standard this
     */
    public function addFallbackControllerDirectory($path, $module = null);
    
    /**
     * @desc Returns the fallback controller directories for a module.
     * @param $module [optional, default to default module] The module name
     * @return Array|null The fallback directories
     */
    public function getFallbackControllerDirectory($module = null);
    
    /**
     * @desc Returns whether a module has fallback controller directories
     * @param $module [optional, default to default module] The module name
     * @return boolean True if this module has fallback controller directories
     */
    public function hasFallbackControllerDirectory($module = null);
    
    /**
     * @desc Sets the fallback controller directoriesm overriding any previous
     * ones.
     * @param $path The path
     * @param $module [optional, default to the default module] The module name
     * @return Standard this
     */
    public function setFallbackControllerDirectory($path, $module = null);
    
    /**
     * @desc Resets the fallback controller directories.
     * 
     * @param $module [optional, default to the default module] The module name
     * @return Standard this
     */
    public function resetFallbackControllerDirectory($module = null);
    
    /**
     * @desc Adds a supported PHP namespace for a module's controllers
     * 
     * @param $namespace The PHP namespace
     * @param $module [optional, default to the default module] The module name
     * @return Standard this
     */
    public function addNamespace($namespace, $module = null);
    
    /**
     * @desc Returns whether a namespace is registered for a module
     * @param $module [optional, default to the default module] The module name
     * @return boolean True if the namespace is registered
     */
    public function hasNamespace($module = null);
    
     /**
     * @desc Returns all the namespace registered for a module
     * @param $module [optional, default to the default module] The module name
     * @return Array With the namespaces, empty array if no namespaces were registered.
     */
    public function getNamespaces($module = null);
}
