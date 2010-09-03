<?php

namespace Majisti\View\Helper;

abstract class AbstractHelper
    extends \Zend_View_Helper_Abstract
    implements \Majisti\Util\Model\Aggregator\IConfig
{
    protected $_config;

    protected $_selector;

    public function getConfig()
    {
        return \Zend_Registry::get('Majisti_Config');
    }

    /**
     *
     * @return \Majisti\Config\Selector The selector
     */
    public function getSelector()
    {
        if( null === $this->_selector ) {
            $this->_selector = new \Majisti\Config\Selector($this->getConfig());
        }

        return $this->_selector;
    }

    public function setConfig(\Zend_Config $config)
    {
        //TODO: complete method stub
    }
}