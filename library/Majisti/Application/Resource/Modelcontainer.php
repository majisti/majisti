<?php

namespace Majisti\Application\Resource;

/**
 * @desc The model container will aggregate a instanciated model container
 * in the registry for further use throughout the MVC application.
 *
 * @author Majisti
 */
class ModelContainer extends \Zend_Application_Resource_ResourceAbstract
{
    /**
     * @desc Inits the model aggregator for cross application
     * model retrieval.
     *
     * @return \Majisti\Model\Container The model container
     */
    public function init()
    {
        return $this->getModelContainer();
    }

    /**
     * @desc Returns the model container.
     * @return \Majisti\Model\Container the container.
     */
    public function getModelContainer()
    {
        if( !\Zend_Registry::isRegistered('Majisti_ModelContainer') ) {
            \Zend_Registry::set('Majisti_ModelContainer',
                new \Majisti\Model\Container());
        }

        return \Zend_Registry::get('Majisti_ModelContainer');
    }
}
