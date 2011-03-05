<?php

namespace Majisti\Application\Extension;

use \Doctrine\Common\ClassLoader;

/**
 * @desc Extensions manager that provides control for loading extensions.
 * Extensions are simply code addition to the library that may contain
 * controller plugins, view helpers, models, controllers, etc.
 *
 * @author Majisti
 */
class Manager
{
    /**
     * @var \Zend_Application
     */
    protected $_application;

    /**
     * @var Array
     */
    protected $_loadedExtensions = array();

    /**
     * @var Array
     */
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
     * @param string $vendor The vendor name
     * @param string $name The extension name
     *
     * @return \Zend_Application_Module_Bootstrap The extension's bootstrap
     *
     * @throws Exception If the bootstrap file is not readable, non existant,
     * has a wrong namespace class name or is not extending AbstractBootstrap
     */
    public function loadExtension($vendor, $name, array $options = array())
    {
        $paths      = $this->getExtensionPaths();
        $triedPaths = array();
        $config     = $this->getApplication()->getBootstrap()->getOption('majisti');

        if( empty($paths) ) {
            throw new Exception("No paths provided.");
        }

        foreach( $paths as $pathInfo ) {
            $triedPaths[]   = $pathInfo['path'];
            $extensionPath  = "{$pathInfo['path']}/{$vendor}/{$name}/lib";

            /* extension dir existance */
            if( !file_exists($extensionPath) ) {
                continue;
            }

            $loader = new ClassLoader($pathInfo['namespace'] . '\\' . $name,
                $extensionPath);
            $loader->register();

            $extensionPath = "{$extensionPath}/{$pathInfo['namespace']}/{$name}";
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

            $pathInfo['path'] = $extensionPath;

            $this->_loadedExtensions[$name] = $pathInfo + array(
                'bootstrap' => $bootstrap
            );

            $this->addBasePath($name, $pathInfo);


            $symlinkName = strtolower("{$pathInfo['namespace']}-{$vendor}-{$name}");
            $symlink = "{$config['app']['path']}/public/" . $symlinkName;
            $target = $pathInfo['path'] . '/public';

            if( !file_exists($symlink) ) {
                @unlink($symlink);
                @symlink($target, $symlink);
            } elseif( @readlink($symlinkName) !== $target ) {
                @unlink($symlink);
                @symlink($target, $symlink);
            }

            $bootstrap->bootstrap();

            return $bootstrap;
        }

        throw new Exception("Extension {$vendor}.{$name} not found using the provided
         paths " . implode(':', $triedPaths));
    }

    /**
     * @desc Adds the extension's base path to the view stack.
     * This is a bit tricky since we want to actually prepend
     * it before the application's library helpers, scripts and filters.
     * This function will remove the application's library base path,
     * append this extension's base path and reapply the application's
     * library basepath thereafter.
     *
     * @param string $name The extension's name
     * @param array $pathInfo The extension's pathinfo
     */
    protected function addBasePath($name, $pathInfo)
    {
        $bootstrap = $this->getApplication()->getBootstrap();
        $maj       = $bootstrap->getOption('majisti');
        $view      = $bootstrap->getResource('view');

        /* extension's base path */
        $view->addBasePath(
            $pathInfo['path'] . '/views',
            "{$pathInfo['namespace']}\\{$name}\View\\"
        );

        $paths = $view->getAllPaths();

        /* remove the application's library base path */
        $i = array_search(
            $maj['app']['path'] . '/library/views/scripts/',
            $paths['script']
        );

        unset($paths['script'][$i]);
        unset($paths['helper'][$maj['app']['namespace'] . '\View\Helper\\']);
        unset($paths['filter'][$maj['app']['namespace'] . '\View\Filter\\']);

        /* reset paths and reappend application's library base path */
        $view->setScriptPath(null);
        $view->setHelperPath(null);
        $view->setFilterPath(null);

        foreach( $paths['script'] as $path ) {
            $view->addScriptPath($path);
        }

        foreach( $paths['helper'] as $prefix => $path ) {
            $view->addHelperPath($path, $prefix);
        }

        foreach( $paths['filter'] as $prefix => $path) {
            $view->addFilterPath($path, $prefix);
        }
    }

    /**
     * @desc Returns a loaded extension bootstrap.
     *
     * @param string $name The extension name
     *
     * @return AbstractBootstrap The bootstrap
     */
    public function getLoadedExtensionBootstrap($name)
    {
        $ext = $this->getLoadedExtension($name);

        return $ext['bootstrap'];
    }

    /**
     * @desc Returns all the loaded extensions infos.
     *
     * @return Array Loaded extensions
     */
    public function getLoadedExtensions()
    {
        return $this->_loadedExtensions;
    }

    /**
     * @desc Returns a loaded extension information.
     *
     * @param string $name The extension name.
     * @return array The extension infos
     */
    public function getLoadedExtension($name)
    {
        return $this->_loadedExtensions[$name];
    }

    /**
     * @desc Returns whether an extension is loaded by that manager.
     *
     * @param string $name The extension's name
     *
     * @return bool True if the extension is loaded by that manager
     */
    public function isExtensionLoaded($name)
    {
        return \array_key_exists($name, $this->_loadedExtensions);
    }
}
