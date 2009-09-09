<?php

namespace Majisti\Controller\Dispatcher;

interface IDispatcher extends \Zend_Controller_Dispatcher_Interface
{
    public function addFallbackControllerDirectory($path, $module = null);
    public function getFallbackControllerDirectory($module = null);
    public function hasFallbackControllerDirectory($module = null);
    public function setFallbackControllerDirectory($path, $module = null);
    public function resetFallbackControllerDirectory($module = null);
    
    public function addNamespace($namespace, $module = null);
    public function hasNamespace($module = null);
    public function getNamespaces($module = null);
}
