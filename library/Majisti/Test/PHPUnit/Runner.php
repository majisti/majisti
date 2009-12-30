<?php

namespace Majisti\Test\PHPUnit;

class Runner extends \PHPUnit_TextUi_TestRunner
{
    protected static $_defaultArguments;
    
    static public function getDefaultArguments()
    {
        if( null === static::$_defaultArguments ) {
            $printer = PHP_SAPI != 'cli'
                ? new Listener\Simple\Html()
                : new Listener\Simple\Text()
            ;
            self::$_defaultArguments = array('printer' => $printer);
        }
        
        return self::$_defaultArguments; 
    }
    
    static public function setDefaultArguments($arguments)
    {
        self::$_defaultArguments = $arguments;
    }
}
