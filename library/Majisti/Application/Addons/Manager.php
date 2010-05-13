<?php

namespace Majisti\Application\Addons;

/**
 * @desc
 * @author Majisti
 */
class Manager
{
    /** @var Array */
    protected $_paths;

    public function hasAddonsNamespace($namespace)
    {
        return array_key_exists($namespace, $this->getAddonsPaths());
    }

    public function registerAddonsPath($path, $namespace)
    {
        $this->_paths[$namespace] = $path;
    }

    public function registerAddonsPaths(array $paths)
    {
        foreach( $paths as $namespace => $path ) {
            $this->registerAddonsPath($path, $namespace);
        }
    }

    public function getAddonsPath($namespace)
    {
        return $this->_paths[$namespace];
    }

    public function getAddonsPaths()
    {
        return $this->_paths;
    }

    public function setAddonsPaths(array $paths)
    {
        $this->_paths = $paths;
    }

    protected function getBootstrapClass($extName, $namespace)
    {
        return "\\$namespace\\Extensions\\$extName\\Bootstrap";
    }

    public function loadExtension($name, $namespace)
    {
        $path           = $this->getAddonsPath($namespace) . "/Extensions/$name";
        $bootstrapFile  = $path . '/Bootstrap.php';

        if( !\Zend_Loader::isReadable($bootstrapFile) ) {
            throw new Exception("Bootstrap cannot be found for extension $name" .
                    ", used path $path with namespace $namespace");
        }

        require_once $bootstrapFile;
        $className = $this->getBootstrapClass($name, $namespace);

        if( !class_exists($className, false) ) {
            throw new Exception("Bootstrap class $className cannot be found" .
                    " for extension $name in namespace $namespace");
        }

        /** @var $bootstrap IAddonsBootstrapper */
        $bootstrap = new $className();
        
        if ( !($bootstrap instanceof IAddonsBootstrapper) ) {
            throw new Exception("Bootstrap class not an instance of " .
                    "\Majisti\Application\Addons\IAddonsBoostrapper " .
                    "for extension $name in namespace $namespace");
        }

        $bootstrap->load();
    }

    public function loadModule($name, $namespace)
    {
        $dispatcher = \Zend_Controller_Front::getInstance()->getDispatcher();
        $path       = $this->getAddonsPath($namespace) .
                      "/Modules/$name/controllers";

        if( !\Zend_Loader::isReadable($path) ) {
            throw new Exception("Module $name is not " .
                    "existant for namespace $namespace");
        }
        /* @var $dispatcher \Majisti\Controller\Dispatcher\Multiple */
        $dispatcher->addFallbackControllerDirectory( $namespace, $path, $name);
    }
}
