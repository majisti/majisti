<?php

namespace Majisti\Util\Model;

/**
 * @desc Class that aggregates a Zend_View_Interface and provides
 * mutators and accessors.
 * 
 * @author Steven Rosato
 */
class ViewAggregate implements IViewAggregate
{
    protected $_view;
    
    /**
     * @link IViewAggregate::getView()
     */
    public function getView()
    {
        return $this->_view;
    }

    /**
     * @link IViewAggregate::setView()
     */
    public function setView(\Zend_View_Interface $view)
    {
        $this->_view = $view;
    }
}