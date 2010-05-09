<?php

namespace Majisti\Controller\Dispatcher;

/**
 * @desc Majisti's Multiple Dispatch supporting multiple fallback controller
 * directories for one module.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Multiple extends \Zend_Controller_Dispatcher_Standard implements IDispatcher
{
    /**
     * @desc Additional controller directories
     * that serve as fallbacks when the controller directory
     * is not found for a module
     * @var Array 2 dimentional
     */
    protected $_controllerFallbackDirectories = array();

    /**
     * @desc Adds a fallback controller directory for a given module.
     *
     * @param namespace The class' PHP namespace
     * @param string $path The path to add
     * @param $module [optional, defaults to default module] The module name
     * @return Multiple this
     */
    public function addFallbackControllerDirectory($namespace, $path, $module = null)
    {
        $namespace  = (string) $namespace;
        $path       = (string) $path;
        $fallDirs   = $this->_controllerFallbackDirectories;

        if( null === $module ) {
            $module = $this->getDefaultModule();
        }

        if( !array_key_exists($module, $fallDirs) ) {
            $fallDirs[$module] = array();
        }

        $fallDirs[$module][] = array($namespace, $path);
        $this->_controllerFallbackDirectories = $fallDirs;

        return $this;
    }

    /**
     * @desc Returns all the fallback controller directories
     *
     * @return Array the controller directories, key`value paired with
     * the module name as the key and the paths as the values within another
     * array.
     */
    public function getFallbackControllerDirectories()
    {
        return $this->_controllerFallbackDirectories;
    }

    /**
     * @desc Adds multiple fallback controller directories at once. If a path
     * is not keyed with a module name within the array, it will assume
     * the default module.
     *
     * @param Array $dirs array(module => array(namespace => array(path))) array
     * @return Multiple this
     */
    public function addFallbackControllerDirectories($dirs)
    {
        foreach ( $dirs as $module => $paths ) {
            $namespace = $paths[0];
            $path      = $paths[1];

            if( !is_string($module) ) {
                $module = $this->getDefaultModule();
            }

            $this->addFallbackControllerDirectory($namespace, $path, $module);
        }

        return $this;
    }

    /**
     * @desc Returns the fallback controller directories for a given module.
     *
     * @param $module [optional, default to default module] The module name
     * @return Array|null The fallback directories
     */
    public function getFallbackControllerDirectory($module = null)
    {
        if( null === $module ) {
            $module = $this->getDefaultModule();
        }

        $fallDirs = $this->_controllerFallbackDirectories;

        if( array_key_exists($module, $fallDirs) ) {
            return $fallDirs[$module];
        }

        return null;
    }

    /**
     * @desc Returns whether a module has fallback controller directories
     * @param $module [optional, default to default module] The module name
     * @return boolean True if this module has fallback controller directories
     */
    public function hasFallbackControllerDirectory($module = null)
    {
        if( null === $module ) {
            $module = $this->getDefaultModule();
        }

        return array_key_exists($module, $this->_controllerFallbackDirectories);
    }

    /**
     * @desc Sets the fallback controller directories overriding any previous
     * ones.
     * @param $controllerDirectories The controller directories
     * @return Multiple this
     */
    public function setFallbackControllerDirectories($controllerDirectories)
    {
        $this->_controllerFallbackDirectories = $controllerDirectories;

        return $this;
    }

    /**
     * @desc Resets the fallback controller directories.
     *
     * @param $module [optional, default to the default module] The module name
     * @return Multiple this
     */
    public function resetFallbackControllerDirectory($module = null)
    {
        $this->_controllerFallbackDirectories = array();

        return $this;
    }

    /**
     * @desc Load a controller class
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
        $loadFile = $this->getDispatchDirectory()
            . DIRECTORY_SEPARATOR . $fileName;

        if( !\Zend_Loader::isReadable($loadFile) ) {
            $fallbackDirs = $this->getFallbackControllerDirectory(
                $this->_curModule);
            foreach( $fallbackDirs as $fallbackDir ) {
                $namespace  = rtrim($fallbackDir[0], '\\') . '\\';
                $path       = $fallbackDir[1];

                if( !\Zend_Loader::isReadable(
                        $path . DIRECTORY_SEPARATOR . $fileName) )
                {
                    continue;
                }
                $this->_curDirectory = $path;

                $finalClass  = $namespace . $className;
                if (($this->_defaultModule != $this->_curModule)
                    || $this->getParam('prefixDefaultModule'))
                {
                    $finalClass = $namespace . $this->formatClassName($this->_curModule, $className);
                }
                if (class_exists($finalClass, false)) {
                    return $finalClass;
                }

                $dispatchDir = $this->getDispatchDirectory();
                $loadFile    = $dispatchDir . DIRECTORY_SEPARATOR . $this->classToFilename($className);

                if (\Zend_Loader::isReadable($loadFile)) {
                    include_once $loadFile;
                } else {
                    require_once 'Zend/Controller/Dispatcher/Exception.php';
                    throw new \Zend_Controller_Dispatcher_Exception('Cannot load controller class "' . $className . '" from file "' . $loadFile . "'");
                }

                if (!class_exists($finalClass, false)) {
                    require_once 'Zend/Controller/Dispatcher/Exception.php';
                    throw new \Zend_Controller_Dispatcher_Exception('Invalid controller class ("' . $finalClass . '")');
                }

                return $finalClass;
            }
        }

        return parent::loadClass($className);
    }

    /**
     * @desc Returns whether a request is dipatchable based on the parent's
     * behaviour and if it is not dispatchable, it will try the fallback
     * controller directories to check whether it is dispatchable or not.
     *
     * @param \Zend_Controller_Request_Abstract $request The request object
     * @return boolean If the request is dispatchable based on additionnal
     * fallback controller directories
     */
    public function isDispatchable(\Zend_Controller_Request_Abstract $request)
    {
        $dispatchable = parent::isDispatchable($request);

        /* When not dispatchable, fallback to the fallback controllers */
        if( !$dispatchable ) {
            $className = $this->getControllerClass($request);

            if( !$className ) {
                return false;
            }

            $fileSpec       = $this->classToFilename($className);
            $fallbackDirs   = $this->getFallbackControllerDirectory(
                    $request->getModuleName());

            if( null === $fallbackDirs ) {
                return false;
            }

            foreach ($fallbackDirs as $fallbackDir) {
                $filePath = $fallbackDir[1] . DIRECTORY_SEPARATOR . $fileSpec;
                if( \Zend_Loader::isReadable($filePath) ) {
                    return true;
                }
            }
        }

        return $dispatchable;
    }
}
