<?php

namespace Majisti\Application;

/**
 * @desc Module Bootrap. This file should not be modified, this is an exact
 * copy of Zend's ModuleBoostrap, but since \Majisti\Application\Bootstrap
 * needed to get inherited, an entire copy was needed. DO NOT MODIFY,
 * this file is untested.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class ModuleBootstrap extends \Majisti\Application\Bootstrap
{
    /**
     * @var \Zend_Loader_Autoloader_Resource
     */
    protected $_resourceLoader;

    /**
     * Constructor
     *
     * @param  \Zend_Application|\Zend_Application_Bootstrap_Bootstrapper $application
     * @return void
     */
    public function __construct($application)
    {
        $this->setApplication($application);

        // Use same plugin loader as parent bootstrap
        if ($application instanceof \Zend_Application_Bootstrap_ResourceBootstrapper) {
            $this->setPluginLoader($application->getPluginLoader());
        }

        $key = strtolower($this->getModuleName());
        if ($application->hasOption($key)) {
            // Don't run via setOptions() to prevent duplicate initialization
            $this->setOptions($application->getOption($key));
        }

        if ($application->hasOption('resourceloader')) {
            $this->setOptions(array(
                'resourceloader' => $application->getOption('resourceloader')
            ));
        }
        $this->initResourceLoader();

        // ZF-6545: prevent recursive registration of modules
        if ($this->hasPluginResource('modules')) {
            $this->unregisterPluginResource('modules');
        }
        
        // ZF-6545: ensure front controller resource is loaded
        if (!$this->hasPluginResource('FrontController')) {
            $this->registerPluginResource('FrontController');
        }
        
        if( !$this->hasPluginResource('ModelContainer') ) {
            $this->registerPluginResource('ModelContainer');
        }
    }

    /**
     * Set module resource loader
     *
     * @param  Zend_Loader_Autoloader_Resource $loader
     * @return Zend_Application_Module_Bootstrap
     */
    public function setResourceLoader(\Zend_Loader_Autoloader_Resource $loader)
    {
        $this->_resourceLoader = $loader;
        return $this;
    }

    /**
     * Retrieve module resource loader
     *
     * @return Zend_Loader_Autoloader_Resource
     */
    public function getResourceLoader()
    {
        if (null === $this->_resourceLoader) {
            $r    = new \ReflectionClass($this);
            $path = $r->getFileName();
            $this->setResourceLoader(new ModuleAutoloader(array(
                'namespace' => $this->getAppNamespace(),
                'basePath'  => dirname($path),
            )));
        }
        return $this->_resourceLoader;
    }

    /**
     * Get default application namespace
     *
     * Proxies to {@link getModuleName()}, and returns the current module
     * name
     *
     * @return string
     */
    public function getAppNamespace()
    {
        $options = $this->getApplication()->getOptions();
        return $options['appnamespace'] . '\\' . $this->getModuleName();
    }


    /**
     * Ensure resource loader is loaded
     *
     * @return void
     */
    public function initResourceLoader()
    {
        $this->getResourceLoader();
    }

    /**
     * Retrieve module name
     *
     * @return string
     */
    public function getModuleName()
    {
        if (empty($this->_moduleName)) {
            $class = get_class($this);
            if (preg_match('/^([a-z][a-z0-9]*)_/i', $class, $matches)) {
                $prefix = $matches[1];
            } else {
                $prefix = $class;
            }
            $this->_moduleName = $prefix;
        }
        return $this->_moduleName;
    }
}
