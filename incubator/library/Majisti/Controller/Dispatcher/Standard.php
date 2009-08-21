<?php

namespace Majisti\Controller\Dispatcher;

class Standard extends \Zend_Controller_Dispatcher_Standard
{
//	protected $_controllerDirectory = array(array());
	
//	public function addControllerDirectory($path, $module = null)
//	{
//		if (null === $module) {
//            $module = $this->_defaultModule;
//        }
//
//        $module = (string) $module;
//        $path   = rtrim((string) $path, '/\\');
//
//        $this->_controllerDirectory[$module][] = $path;
//        
////        \Zend_Debug::dump($this->_controllerDirectory);
//        return $this;
//	}
//	
//	public function isDispatchable(\Zend_Controller_Request_Abstract $request)
//    {
//        $className = $this->getControllerClass($request);
//        
//        if (!$className) {
//            return false;
//        }
//
//        if (class_exists($className, false)) {
//            return true;
//        }
//        \Zend_Debug::dump($this->getDispatchDirectory());
//
//        $fileSpec    = $this->classToFilename($className);
//        $dispatchDirectories = $this->getDispatchDirectory();
//        
//        foreach ($dispatchDirectories as $dispatchDir) {
//        	if( \Zend_Loader::isReadable($dispatchDir . DIRECTORY_SEPARATOR . $fileSpec) ) {
//        		return true;
//        	}
//        }
//        
//        return false;
//    }
}
