<?php

namespace Majisti\Application\Extension;

abstract class AbstractBootstrap extends \Majisti\Application\ModuleBootstrap
{
    public function __construct($application)
    {
        parent::__construct($application);

        $this->initResourcePaths();
    }

    /**
     * @desc Adds the application's resource path to the plugin loader stack.
     */
    protected function initResourcePaths()
    {
        //FIXME: does not seem to work
        $rc = new \ReflectionClass($this);

        $this->getPluginLoader()->addPrefixPath(
            $rc->getNamespaceName()     . '\Application\Resource\\',
            dirname($rc->getFileName()) . '/resources'
        );

//        $this->registerPluginResource('Foo')->bootstrap('Foo');
    }

    public function getAppNamespace()
    {
        return rtrim(str_replace('Bootstrap', '', $this->getModuleName()), '\\');
    }
}
