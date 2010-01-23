<?php

namespace Majisti\Application\Resource;

class Dispatcher extends \Zend_Application_Resource_ResourceAbstract
{
    /**
     * @desc Inits the standard dispatcher that supports multiple controller
     * directories for a single module and PHP namespaces.
     * @return \Majisti\Dispatcher\Standard The dispatcher
     */
    public function init()
    {
        return $this->getDispatcher();
    }
    
    public function getDispatcher()
    {
        $this->_bootstrap->bootstrap('FrontController');
        $front = $this->_bootstrap->getResource('FrontController');
        
        $dispatcher = new \Majisti\Controller\Dispatcher\Multiple();
        $dispatcher->setControllerDirectory($front->getControllerDirectory());
        $dispatcher->addFallbackControllerDirectory(
           MAJISTIX_MODULES . '/default/controllers');
        $front->setDispatcher($dispatcher);

        return $dispatcher;
    }
}
