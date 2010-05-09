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
     * @var \Majisti\Controller\Dispatcher\Multiple 
     */
    protected $_dispatcher;

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
        if( null === $this->_dispatcher ) {
            $this->_bootstrap->bootstrap('FrontController');
            $front = $this->_bootstrap->getResource('FrontController');

            $dispatcher = new \Majisti\Controller\Dispatcher\Multiple();
            $dispatcher->setControllerDirectory($front->getControllerDirectory());
            $front->setDispatcher($dispatcher);

            $this->_dispatcher = $dispatcher;

            $this->resolveFallbacks();
        }

        return $this->_dispatcher;
    }

    /**
     * @desc Resolves any fallback added in the configuration and adds it
     * to the dispatcher.
     */
    protected function resolveFallbacks()
    {
        $dispatcher = $this->getDispatcher();
        $selector   = new \Majisti\Config\Selector(
            new \Zend_Config($this->getOptions()));

        /* add the fallbacks */
        if( $fallbacks = $selector->find('fallback', false) ) {
            foreach ($fallbacks as $module => $fallback) {
                $fallback = $fallback->toArray();
                $namespace = current($fallback);
                $path      = key($fallback);

                $dispatcher->addFallbackControllerDirectory(
                    $namespace, $path, $module);
            }
        }
    }
}
