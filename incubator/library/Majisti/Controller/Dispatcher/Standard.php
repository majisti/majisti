<?php

namespace Majisti\Controller\Dispatcher;

/**
 * @desc Majisti's Standard Dispatcher supporting namespaces
 * and multiple fallback controller directories for one module.
 * 
 * @package Majisti\Controller\Dispatcher;
 * @author Steven Rosato
 */
class Standard extends \Zend_Controller_Dispatcher_Standard implements IDispatcher
{
    /**
     * @desc Additional controller directories
     * that serve as fallbacks when the controller directory
     * is not found for a module
     * @var Array 2 dimentional
     */
    protected $_controllerFallbackDirectories = array();
    
    /**
     * @desc Provides support for namespaced controllers.
     * The last namespace added is the first one checked when
     * loading the controller class (LIFO)
     * @var Array
     */
    protected $_namespaces = array();
    
    public function addFallbackControllerDirectory($path, $module = null)
    {
        if( null === $module ) {
            $module = $this->getDefaultModule();
        }
        
        if( array_key_exists($module, $this->_controllerFallbackDirectories) ) {
            $this->_controllerFallbackDirectories[$module][] = $path;
        } else {
            $this->_controllerFallbackDirectories[$module] = array($path);
        }
        
//        $view = \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
//        $view->addBasePath(realpath($path . '/../views'));
        
        return $this;
    }
    
    public function getFallbackControllerDirectory($module = null)
    {
        if( null === $module ) {
            $module = $this->getDefaultModule();
        }
        
        if( array_key_exists($module, $this->_controllerFallbackDirectories) ) {
            return $this->_controllerFallbackDirectories[$module];
        }
        
        return null;
    }
    
    public function hasFallbackControllerDirectory($module = null)
    {
        throw new \Majisti\Util\Exception\NotImplementedException();
    }
    
    public function setFallbackControllerDirectory($path, $module = null)
    {
        throw new \Majisti\Util\Exception\NotImplementedException();    
    }
    
    public function resetFallbackControllerDirectory($module = null)
    {
        $this->_controllerFallbackDirectories = array();
    }
    
    /**
     * Load a controller class
     *
     * Attempts to load the controller class file from
     * {@link getControllerDirectory()}.  If the controller belongs to a
     * module, looks for the module prefix to the controller class.
     *
     * @param string $className
     * @return string Class name loaded
     * @throws \Zend_Controller_Dispatcher_Exception if class not loaded
     */
    public function loadClass($className)
    {
        $fileName = $this->classToFilename($className);
        $loadFile = $this->getDispatchDirectory() . DIRECTORY_SEPARATOR . $fileName;
        
        try {
            if( !\Zend_Loader::isReadable($loadFile) ) {
                foreach( $this->getFallbackControllerDirectory($this->_curModule) as $dir ) {
                    if( !\Zend_Loader::isReadable($dir . DIRECTORY_SEPARATOR . $fileName) ) {
                        continue;
                    } 
                    $this->_curDirectory = $dir;
                    return parent::loadClass($className);
                }
            }
            $finalClass = parent::loadClass($className);
        } catch( \Zend_Controller_Dispatcher_Exception $e ) {
            if (($this->_defaultModule !== $this->_curModule)
                || $this->getParam('prefixDefaultModule'))
            {
                $finalClass = $this->formatClassName($this->_curModule, $className);
            }

            if (!class_exists($finalClass, false)) {
                foreach (array_reverse($this->getNamespaces($this->_curModule)) as $namespace) {
                    if( class_exists($namespace . $finalClass, false) ) {
                        return $namespace . $finalClass;
                    }
                }
                throw $e;
            }
        }
        
        return $finalClass;
    }
    
    public function isDispatchable(\Zend_Controller_Request_Abstract $request)
    {
        $dispatchable = parent::isDispatchable($request);
        
        if( !$dispatchable ) {
            $className = $this->getControllerClass($request);
            
            if( !$className ) {
                return false;
            }
            
            $fileSpec = $this->classToFilename($className);
            $fallbackDirs = $this->getFallbackControllerDirectory($request->getModuleName());
            
            if( null === $fallbackDirs ) {
                return false;
            }
            
            foreach ($fallbackDirs as $fallbackDir) {
                $filePath = $fallbackDir . DIRECTORY_SEPARATOR . $fileSpec;
                if( \Zend_Loader::isReadable($filePath) ) {
                    return true;
                }
            }
        }
        
        return $dispatchable;
    }
    
    public function addNamespace($namespace, $module = null)
    {
        if( null === $module ) {
            $module = $this->getDefaultModule();
        }
        
        if( array_key_exists($module, $this->_namespaces) ) {
            $this->_namespaces[$module][] = $namespace;
        } else {
            $this->_namespaces[$module] = array($namespace);
        }
        
        return $this;
    }
    
    
    public function hasNamespace($module = null) 
    {
        if( null === $module ) {
            $module = $this->getDefaultModule();
        }
        
        return array_key_exists($module, $this->_namespaces);
    }
    
    public function getNamespaces($module = null) 
    {
        if( null === $module ) {
            $module = $this->getDefaultModule();
        }
        
        return $this->hasNamespace($module)
            ? $this->_namespaces[$module]
            : array();
    }
}
