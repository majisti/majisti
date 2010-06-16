<?php

namespace Majisti;

class Model extends    \ArrayObject
            implements \Majisti\Util\Model\Aggregator\IView
{
    protected $_view;

    protected $_data;

    public function __construct($data = null, $view = null)
    {
        $this->_data = $data;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function setView(\Zend_View_Interface $view)
    {
        $this->_view = $view;
    }

    public function getView()
    {
        if( null === $this->_view ) {
            $this->_view = \Zend_Registry::get('Zend_View');
        }

        return $this->_view;
    }
}
