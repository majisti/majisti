<?php

namespace Majisti\Application\Addons;

abstract class AbstractBootstrap extends \Majisti\Application\ModuleBootstrap
{
    public function getAppNamespace()
    {
        return rtrim(str_replace('Bootstrap', '', $this->getModuleName()), '\\');
    }
}
