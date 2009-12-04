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
    	$dispatcher = new \Majisti\Controller\Dispatcher\Standard();
    	$dispatcher->addFallbackControllerDirectory(
    	   MAJISTIX_MODULES_PATH . '/default/controllers');
    	\Zend_Controller_Front::getInstance()->setDispatcher($dispatcher);
    	
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
        return new \Majisti\Model\Container();
    }
    
    /**
     * @desc Anything related after the construction of the bootstrap class
     */
    protected function _postConstruct() {}
}
