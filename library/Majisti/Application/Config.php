<?php
namespace Majisti\Application;

class Config extends \Zend_Config
{
    static protected $_runningInstance;

    public function init()
    {

    }

    protected function defineSettings()
    {

    }

    static public function getRunningInstance()
    {
        return static::$_runningInstance();
    }

    public function setAsRunningInstance()
    {
        static::$_runningInstance = $this;
    }

    public function defineConstants($useAliases)
    {

    }
}
