<?php

namespace Majisti\Util\Minifying;

class Factory
{
    static public function createMinifier($class, $options = array())
    {
        /* TODO: support more than just the namespaced algorithms */
        $class = __NAMESPACE__ . "\\$class";

        return new $class($options);
    }
}