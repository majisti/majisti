<?php

namespace Majisti\Util\Pattern;

abstract class SingletonAbstract implements ISingleton
{
    protected function __construct()
    {}
    
    static public function getInstance()
    {
        if( null === static::$_instance ) {
            static::$_instance = new static();
        }
        
        return static::$_instance;
    }
}
