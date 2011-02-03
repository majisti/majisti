<?php

namespace Majisti\View\Helper;

/**
 * @desc Abstract Helper for view helpers providing configuration and
 * a selector.
 *
 * @author Majisti
 */
abstract class AbstractHelper extends \Zend_View_Helper_Abstract
{
    /**
     * @var \Zend_Config 
     */
    protected $_config;

    /**
     * @var \Majisti\Config\Selector
     */
    protected $_selector;

    /**
     * @desc Returns the Majisti config.
     *
     * @return \Zend_ Config The Majisti config.
     */
    public function getConfig()
    {
        return \Zend_Registry::get('Majisti_Config');
    }

    /**
     * @desc Returns a config selector based on this helper
     * configuration
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

    /**
     * @desc Returns the view.
     *
     * @return \Zend_View The view
     */
    public function getView()
    {
        return $this->view;
    }
}