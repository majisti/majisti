<?php

namespace Majisti\Controller\Plugin;

/**
 * @desc Abstract plugin for controller plugins, provides a view and configuration
 * aggregation.
 *
 * @author Majisti
 */
abstract class AbstractPlugin extends \Zend_Controller_Plugin_Abstract
{
    /**
     * @var \Zend_View_Interface
     */
    protected $_view;
    
    /**
     * @var \Zend_Config
     */
    protected $_config;
    
    public function __construct(\Zend_Config $config = null)
    {
        $this->_config = $config;
    }

    /**
     * @desc Returns the config.
     *
     * @return \Zend_Config the config
     */
    public function getConfig()
    {
        if( null === $this->_config && \Zend_Registry::isRegistered('Majisti_Config') ) {
            $this->_config = \Zend_Registry::get('Majisti_Config');
        }
        
        return $this->_config;
    }

    /**
     * @desc Sets the config.
     *
     * @param \Zend_Config $config The config
     */
    public function setConfig(\Zend_Config $config)
    {
        $this->_config = $config;
    }
    
    /**
     * @desc Returns the view.
     *
     * @return \Zend_View_Interface
     */
    public function getView()
    {
        if( null === $this->_view ) {
            $this->_view = \Zend_Registry::isRegistered('Zend_View')
                ? \Zend_Registry::get('Zend_View')
                : new \Majisti\View\View();
        }
        
        return $this->_view;
    }

    /**
     * @desc Sets the view.
     *
     * @param \Zend_View_Interface $view The view
     */
    public function setView(\Zend_View_Interface $view)
    {
        $this->_view = $view;
    }
}