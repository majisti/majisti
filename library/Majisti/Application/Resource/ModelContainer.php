<?php

namespace Majisti\Application\Resource;

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
    
    public function getModelContainer()
    {
        if( !\Zend_Registry::isRegistered('Majisti_ModelContainer') ) {
            \Zend_Registry::set('Majisti_ModelContainer', new \Majisti\Model\Container());
        }

        return \Zend_Registry::get('Majisti_ModelContainer');
    }
}