<?php

namespace Majisti\Config\Handler;

class Import implements IHandler
{
    /**
     * This is used to track imports inheritance. The keys are names of imports
     * and the values are the imported configs.
     *
     * @var array
     */
    protected $_imports = array();
    
    public function handle(\Zend_Config $config)
    {
        throw new \Majisti\Util\Exception\NotImplementedException();
    }
}