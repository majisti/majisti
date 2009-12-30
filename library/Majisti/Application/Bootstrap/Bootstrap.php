<?php

namespace Majisti\Application\Bootstrap;

/**
 * @desc Majisti's application boostrap.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Bootstrap extends \Zend_Application_Bootstrap_Bootstrap
{
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
}
