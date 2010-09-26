<?php

namespace Majisti\Test;

/**
 * @desc Runner class for Majisti. Uses the Html listener within a browser
 * and the Text listener within the CLI.
 *
 * @author Majisti
 */
class Runner extends \PHPUnit_TextUI_TestRunner
{
    protected static $_defaultArguments;

    /**
     * @desc Returns an array of arguments. The default listener will be
     * set according to wheter the test is running within a browser of if
     * it is running within the CLI.
     *
     * @return array The arguments
     */
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

    /**
     * @desc Sets the default arguments for the runner.
     *
     * @param array $arguments The arguments
     */
    static public function setDefaultArguments(array $arguments)
    {
        self::$_defaultArguments = $arguments;
    }
}
