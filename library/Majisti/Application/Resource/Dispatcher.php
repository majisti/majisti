<?php

namespace Majisti\Application\Resource;

/**
 * @desc The dispatcher resource provides a default Dispatcher which is
 * the MultipleDispatcher that can dispatch multiple controller directories.
 *
 * @see \Majisti\Controller\Dispatcher\Multiple
 */
class Dispatcher extends \Zend_Application_Resource_ResourceAbstract
{
    /**
     * @desc Inits the standard dispatcher that supports multiple controller
     * directories for a single module and PHP namespaces.
     *
     * @return \Majisti\Controller\Dispatcher\Multiple The dispatcher
     */
    public function init()
    {
        return $this->getDispatcher();
    }

    /**
     * @desc Returns the multiple dispatcher. By default, it adds
     * an additionnal fallback controller directory to the default module
     * which points to MajistiX.
     *
     * @return \Majisti\Controller\Dispatcher\Multiple The dispatcher
     */
    public function getDispatcher()
    {
        $this->_bootstrap->bootstrap('FrontController');
        $front = $this->_bootstrap->getResource('FrontController');
        
        $dispatcher = new \Majisti\Controller\Dispatcher\Multiple();
        $dispatcher->setControllerDirectory($front->getControllerDirectory());
        $dispatcher->addFallbackControllerDirectory('\MajistiX\Modules',
           MAJISTIX_MODULES . '/default/controllers');
        $front->setDispatcher($dispatcher);

        return $dispatcher;
    }
}
