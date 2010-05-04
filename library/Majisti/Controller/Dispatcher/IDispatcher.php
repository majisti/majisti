<?php

namespace Majisti\Controller\Dispatcher;

/**
 * @desc Dispatcher interface that adds fallback controller directories.
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
     * @desc Returns all the fallback controller directories
     *
     * @return Array the controller directories, key`value paired with
     * the module name as the key and the paths as the values within another
     * array.
     */
    public function getFallbackControllerDirectories();

    /**
     * @desc Sets the fallback controller directories overriding any previous
     * ones.
     * @param $controllerDirectories The controller directories
     * @return Multiple this
     */
    public function setFallbackControllerDirectories($controllerDirectories);
    
    /**
     * @desc Resets the fallback controller directories.
     * 
     * @param $module [optional, default to the default module] The module name
     * @return Standard this
     */
    public function resetFallbackControllerDirectory($module = null);
}
