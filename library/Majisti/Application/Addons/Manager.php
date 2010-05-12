<?php

namespace Majisti\Application\Addons;

/**
 * @desc Addons manager that provides control for loading addons.
 * Addons consists of two different concepts: the Extensions and
 * the Modules. Extensions are simply code addition to the library
 * that may contain controller plugins, view helpers, models, controllers,
 * etc. whereas Modules are simply standard Zend modules that can be
 * dispatched. Any added modules are considered a fallback module dispatchable
 * by Majisti's Multiple Dispatcher.
 *
 * @author Majisti
 */
class Manager
{
    /** @var Array */
    protected $_paths;

    /**
     * @desc Checks wheter a namespace was registered beforehand when
     * a path was registered.
     *
     * @param string $namespace The namespace that should be used
     * @return bool True is the namespace is used for a certain path.
     */
    public function hasAddonsNamespace($namespace)
    {
        return array_key_exists($namespace, $this->getAddonsPaths());
    }

    /**
     * @desc Registers a path for addons dropins. This is where the
     * Extensions and Modules directories should be placed. The
     * addon will be loadable with the namespace provided for it.
     *
     * @param string $path The addons path
     * @param string $namespace The namespace is should operate under
     */
    public function registerAddonsPath($path, $namespace)
    {
        $this->_paths[$namespace] = $path;
    }

    /**
     * @desc Registers multiple addons path at once.
     *
     * @param array $paths The paths, following a $namespace => $path
     * key/value pairing.
     *
     * @see registerAddonsPath()
     * @return Manager this
     */
    public function registerAddonsPaths(array $paths)
    {
        foreach( $paths as $namespace => $path ) {
            $this->registerAddonsPath($path, $namespace);
        }

        return $this;
    }

    /**
     * @desc Returns the path for a given namespace .
     *
     * @param string $namespace The namespace
     * @return string The path
     * @throws Exception if the namespace was never used
     */
    public function getAddonsPath($namespace)
    {
        if( !array_key_exists($namespace, $this->_paths) ) {
            throw new Exception("Namespace $namespace was never used");
        }

        return $this->_paths[$namespace];
    }

    /**
     * @desc Returns all the addons path in a $namespace => $path key/value
     * pairing.
     *
     * @return Array The array containing the namespaces and paths
     * as key/value pairing
     */
    public function getAddonsPaths()
    {
        return $this->_paths;
    }

    /**
     * @desc Sets all the addons path at once using a $namespace => $path
     * key/value pairing.
     *
     * @param array $paths The paths
     *
     * @return Manager this
     */
    public function setAddonsPaths(array $paths)
    {
        $this->_paths = $paths;

        return $this;
    }

    /**
     * @desc Returns the bootstap class for an extension and namespace. It makes
     * use of PHP namespaces.
     *
     * @param string $extName The extension name
     * @param string $namespace The namespace
     * @return string The formatted bootstrap class using PHP namespaces
     */
    protected function getBootstrapClass($extName, $namespace)
    {
        return "\\$namespace\\Extensions\\$extName\\Bootstrap";
    }

    /**
     * @desc Loads an extension, calling its respective bootstrap.
     *
     * @param string $name The extension name
     * @param string $namespace The namespace it operates under
     * @return undetermined yet
     * @throws Exception If the bootstrap file is not readable, non existant,
     * has wrong namespaced class name or is not implementing IAddonsBoostrapper
     */
    public function loadExtension($name, $namespace)
    {
        $path           = $this->getAddonsPath($namespace) . "/Extensions/$name";
        $bootstrapFile  = $path . '/Bootstrap.php';

        /* file existance */
        if( !\Zend_Loader::isReadable($bootstrapFile) ) {
            throw new Exception("Bootstrap cannot be found for extension $name" .
                    ", used path $path with namespace $namespace");
        }

        /* format class name using PHP namespaces */
        require_once $bootstrapFile;
        $className = $this->getBootstrapClass($name, $namespace);

        /* check for class existance */
        if( !class_exists($className, false) ) {
            throw new Exception("Bootstrap class $className cannot be found" .
                    " for extension $name in namespace $namespace");
        }

        /** @var $bootstrap IAddonsBootstrapper */
        $bootstrap = new $className();

        /* must comply to the interface for dependency resolving (todo) */
        if ( !($bootstrap instanceof IAddonsBootstrapper) ) {
            throw new Exception("Bootstrap class not an instance of " .
                    "\Majisti\Application\Addons\IAddonsBoostrapper " .
                    "for extension $name in namespace $namespace");
        }

        return $bootstrap->load();
    }

    /**
     * @desc Loads a module.
     *
     * @param string $name The module name
     * @param string $namespace The namespace it operates under
     * @return undetermined yet
     * @throws Exception If the modules's controllers directory is not
     * readable or existant.
     */
    public function loadModule($name, $namespace)
    {
        $dispatcher = \Zend_Controller_Front::getInstance()->getDispatcher();
        $path       = $this->getAddonsPath($namespace) .
                      "/Modules/$name/controllers";

        /* checks whether path is readable */
        if( !\Zend_Loader::isReadable($path) ) {
            throw new Exception("Module $name is not " .
                    "existant for namespace $namespace");
        }

        /** @var $dispatcher \Majisti\Controller\Dispatcher\Multiple */
        $dispatcher->addFallbackControllerDirectory($namespace, $path, $name);
    }
}
