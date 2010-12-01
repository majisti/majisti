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
    protected $_extensionPaths;

    /**
     * @desc Returns all the addons path in a $namespace => $path key/value
     * pairing.
     *
     * @return Array The array containing the namespaces and paths
     * as key/value pairing
     */
    public function getExtensionPaths()
    {
        return $this->_extensionPaths;
    }

    /**
     * @desc Sets all the addons path at once using a $namespace => $path
     * key/value pairing.
     *
     * @param array $paths The paths
     *
     * @return Manager this
     */
    public function setExtensionPaths(array $paths)
    {
        $this->_extensionPaths = $paths;

        return $this;
    }

    /**
     * @desc Returns the bootstrap class for an extension and namespace. It makes
     * use of PHP namespaces.
     *
     * @param string $extName The extension name
     * @param string $namespace The namespace
     * @return string The formatted bootstrap class using PHP namespaces
     */
    protected function getExtensionBootstrapClass($extName, $namespace)
    {
        return "\\$namespace\\Extension\\$extName\\Bootstrap";
    }

    /**
     * @desc Loads an extension, calling its respective bootstrap.
     *
     * @param string $name The extension name
     * @return undetermined yet
     *
     * @throws Exception If the bootstrap file is not readable, non existant,
     * has wrong namespaced class name or is not implementing IAddonsBoostrapper
     */
    public function loadExtension($name)
    {
        $paths = $this->getExtensionPaths();
        $triedPaths = array();

        foreach( $paths as $pathInfo ) {
            $triedPaths[]   = $pathInfo['path'];
            $bootstrapFile  = "{$pathInfo['path']}/{$name}/Bootstrap.php";

            /* file existance */
            if( !\Zend_Loader::isReadable($bootstrapFile) ) {
                throw new Exception("Bootstrap file not found " .
                    "for extension {$name}, using {$bootstrapFile}.");
            }

            /* format class name using PHP namespaces */
            require_once $bootstrapFile;
            $className = $this->getExtensionBootstrapClass(
                $name, $pathInfo['namespace']);

            /* check for class existance */
            if( !class_exists($className, false) ) {
                throw new Exception("Bootstrap class {$className} " .
                    "not found for extension {$name}.");
            }

            /** @var $bootstrap IAddonsBootstrapper */
            $bootstrap = new $className();

            /* must comply to the interface for dependency resolving */
            if ( !($bootstrap instanceof IAddonsBootstrapper) ) {
                throw new Exception("Bootstrap class not an instance of " .
                    "\Majisti\Application\Addons\IAddonsBoostrapper " .
                    "for extension {$name} " .
                    "in namespace {$pathInfo['namespace']}");
            }

            return $bootstrap->load();
        }

        throw new Exception("Extension {$name} not found using the provided
         paths " . implode(':', $triedPaths));
    }

    /**
     * @desc Loads a module.
     *
     * @param string $name The module name
     * @param string $namespace The namespace it operates under
     * @return undetermined yet
     * @throws Exception If the modules's controllers directory is not
     * readable or inexistant.
     */
//    public function loadModule($name, $namespace)
//    {
//        $dispatcher = \Zend_Controller_Front::getInstance()->getDispatcher();
//        $path       = $this->getExtensionPath($namespace) .
//                      "/Modules/$name/controllers";
//
//        /* checks whether path is readable */
//        if( !\Zend_Loader::isReadable($path) ) {
//            throw new Exception("Module $name is not " .
//                    "existant for namespace $namespace");
//        }
//
//        /** @var $dispatcher \Majisti\Controller\Dispatcher\Multiple */
//        $dispatcher->addFallbackControllerDirectory($namespace, $path, $name);
//    }
}
