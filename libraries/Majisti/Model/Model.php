<?php

namespace Majisti\Model;

/**
 * @desc The majisti model.
 *
 * @author Majisti
 */
class Model extends \ArrayObject
{
    /**
     * @var \Zend_View
     */
    protected $_view;

    /**
     * @var mixed
     */
    protected $_data;

    /**
     * @desc Constructs the model.
     *
     * @param mixed $data The data
     * @param \Zend_View $view The view
     */
    public function __construct($data = null, $view = null)
    {
        $this->_data = $data;
    }

    /**
     * @desc Returns the data.
     *
     * @return mixed The data
     */
    public function getData()
    {
        return $this->_data;
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

    /**
     * @desc Returns the view. In case no view was provided, it attemps to retrieve
     * a \Zend_View within the \Zend_Registry.
     *
     * @return \Zend_View The view
     */
    public function getView()
    {
        if( null === $this->_view ) {
            $this->setView(\Zend_Registry::get('Zend_View'));
        }

        return $this->_view;
    }
}
