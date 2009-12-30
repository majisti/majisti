<?php

abstract class MutableSingletonAbstract extends SingletonAbstract
{
    static public function setInstance(IMutableSingleton $instance)
    {
        static::$_instance = $instance;
    }
}