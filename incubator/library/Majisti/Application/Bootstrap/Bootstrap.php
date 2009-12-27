<?php

namespace Majisti\Application\Bootstrap;

/**
 * @desc Majisti's application boostrap.
 *
 * @author Steven Rosato
 * @version 1.0
 */
class Bootstrap extends \Zend_Application_Bootstrap_Bootstrap
{
    /**
     * @desc Constructs the boostrap class and calls the postConstruct()
     * method after instanciation.
     *
     * @param $application The Majisti's application
     */
    public function __construct($application)
    {
        parent::__construct($application);
        $this->_postConstruct();
    }

    /**
     * @desc Inits the standard dispatcher that supports multiple controller
     * directories for a single module and PHP namespaces.
     * @return \Majisti\Dispatcher\Standard The dispatcher
     */
    protected function _initDispatcher()
    {
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');

    	$dispatcher = new \Majisti\Controller\Dispatcher\Standard();
    	$dispatcher->setControllerDirectory($front->getControllerDirectory());
    	$dispatcher->addFallbackControllerDirectory(
    	   MAJISTIX_MODULES_PATH . '/default/controllers');
    	$front->setDispatcher($dispatcher);

    	return $dispatcher;
    }

    /**
     * @desc Inits the model aggregator for cross application
     * model retrieval.
     *
     * @return \Majisti\Model\Container The model container
     */
    protected function _initModelContainer()
    {
        if( !\Zend_Registry::isRegistered('Majisti_ModelContainer') ) {
            \Zend_Registry::set('Majisti_ModelContainer', new \Majisti\Model\Container());
        }

        return \Zend_Registry::get('Majisti_ModelContainer');
    }

    /**
     * @desc Anything related after the construction of the bootstrap class
     */
    protected function _postConstruct() {}
}
