<?php

namespace Majisti\Application\Addons;

abstract class AbstractBootstrap extends \Majisti\Application\ModuleBootstrap
{
    protected function _initHelperPaths()
    {
        /* @var $view \Zend_View */
        $view = $this->getApplication()->getBootstrap()
                     ->getResource('View');

        $reflector = new \ReflectionClass($this);
        $view->addHelperPath(
            dirname($reflector->getFileName()) . '/views/helpers',
            $reflector->getNamespaceName() . '\View\Helper\\'
        );
    }

    public function getAppNamespace()
    {
        return rtrim(str_replace('Bootstrap', '', $this->getModuleName()), '\\');
    }
}
