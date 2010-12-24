<?php

namespace Majisti\Controller\Plugin;

abstract class AbstractPlugin
    extends     \Zend_Controller_Plugin_Abstract
     
    implements  \Majisti\Util\Model\Aggregator\IView,
                \Majisti\Util\Model\Aggregator\IConfig
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
    
    public function getConfig()
    {
        if( null === $this->_config && \Zend_Registry::isRegistered('Majisti_Config') ) {
            $this->_config = \Zend_Registry::get('Majisti_Config');
        }
        
        return $this->_config;
    }
    
    public function setConfig(\Zend_Config $config)
    {
        $this->_config = $config;
    }
    
    /**
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
    
    public function setView(\Zend_View_Interface $view)
    {
        $this->_view = $view;
    }
}