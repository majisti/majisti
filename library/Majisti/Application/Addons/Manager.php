<?php

namespace Majisti\Application\Addons;

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

    public function getAddonsPaths()
    {
        return $this->_paths;
    }

    public function setAddonsPaths(array $paths)
    {
        $this->_paths = $paths;
    }

    public function loadExtension($name, $namespace)
    {
        $extPath        = $this->getBasePath() . DIRECTORY_SEPARATOR . $name;
        $bootstrapFile  = $extPath . '/Bootstrap.php';

        if( !\Zend_Loader::isReadable($bootstrapFile) ) {
            throw new Exception("Bootstrap cannot be found for extension $name");
        }

        require_once $bootstrapFile;
        $className = $this->getNamespace() . "\\$name";

        if( !class_exists($className, false) ) {
            throw new Exception("Bootstrap class $className cannot be found" .
                    " for extension $name");
        }

        /** @var $bootstrap IAddonsBootstrapper */
        $bootstrap = new $className();
        
        if ( !($bootstrap instanceof IAddonsBootstrapper) ) {
            throw new Exception("Bootstrap class not an instance of " .
                    "\Majisti\Application\Addons\IAddonsBoostrapper " .
                    "for extension $name");
        }

        $bootstrap->load();
    }

    public function loadModule($name, $namespace)
    {

    }
}
