<?php

namespace Majisti\Application;

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

    	$dispatcher = new \Majisti\Controller\Dispatcher\Multiple();
    	$dispatcher->setControllerDirectory($front->getControllerDirectory());
    	$dispatcher->addFallbackControllerDirectory(
    	   MAJISTIX_MODULES . '/default/controllers');
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
     * @desc Inits a null translator.
     *
     * @return \Zend_Translate_Adapter_Abstract
     */
    protected function _initTranslation()
    {
        \Zend_Registry::set('Zend_Translate', new \Zend_Translate_Adapter_Array(
            array(), null, array('disableNotices' => true)), array());

        return \Zend_Registry::get('Zend_Translate');
    }

    /**
     * @desc Inits the application's library autoloader
     * which is basically the same as an module autoloader.
     *
     * @return \Zend_Application_Module_Autoloader
     */
    protected function _initLibraryAutoloader()
    {
        $autoloader = \Zend_Loader_Autoloader::getInstance();
        $libAutoloader = new \Zend_Application_Module_Autoloader(array(
            'basePath'  => APPLICATION_LIBRARY,
            'namespace' => APPLICATION_NAME
        ));

        $autoloader->pushAutoloader($libAutoloader);

        return $libAutoloader;
    }
}
