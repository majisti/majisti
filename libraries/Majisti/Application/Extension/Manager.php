<?php

namespace Majisti\Application\Extension;

/**
 * @desc Addons manager that provides control for loading addons.
 * Addons consists of two different concepts: the Extensions and
 * the Modules. Extensions are simply code addition to the library
 * that may contain controller plugins, view helpers, models, controllers,
 * etc. whereas Modules are pretty much the same as Extensions, but with
 * the addition of being dispatchable. Any added modules are considered
 * a fallback module dispatchable by Majisti's Multiple Dispatcher.
 *
 * @author Majisti
 */
class Manager
{
    protected $_application;

    protected $_loadedExtensions = array();

    /** @var Array */
    protected $_extensionPaths = array();

    /**
     * @desc Constructs the addons manager.
     *
     * @param \Zend_Application $application The application
     */
    public function __construct(\Zend_Application $application)
    {
        $this->_application = $application;
    }

    /**
     * @desc Returns the application.
     *
     * @return \Zend_Application The application
     */
    public function getApplication()
    {
        return $this->_application;
    }

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
        return "\\$namespace\\$extName\\Bootstrap";
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
    public function loadExtension($name, array $options = array())
    {
        $paths      = $this->getExtensionPaths();
        $triedPaths = array();

        if( empty($paths) ) {
            throw new Exception("No paths provided.");
        }

        foreach( $paths as $pathInfo ) {
            $triedPaths[]   = $pathInfo['path'];
            $extensionPath  = "{$pathInfo['path']}/{$name}";

            /* extension dir existance */
            if( !file_exists($extensionPath) ) {
                continue;
            }

            $bootstrapFile = "{$extensionPath}/Bootstrap.php";

            /* bootstrap file existance */
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

            $bootstrap = new $className($this->getApplication());

            /* must comply to the interface for dependency resolving */
            if ( !($bootstrap instanceof AbstractBootstrap) ) {
                throw new Exception("Bootstrap class not an instance of " .
                    "\Majisti\Application\Extension\AbstractBootstrap " .
                    "for extension {$name} " .
                    "in namespace {$pathInfo['namespace']}");
            }

            $bootstrap->setOptions($options);

            $this->_loadedExtensions[$name] = $pathInfo;

            return $bootstrap->bootstrap();
        }

        throw new Exception("Extension {$name} not found using the provided
         paths " . implode(':', $triedPaths));
    }

    public function getLoadedExtensions()
    {
        return $this->_loadedExtensions;
    }
}
