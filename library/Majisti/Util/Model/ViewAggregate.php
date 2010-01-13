<?php

namespace Majisti\Util\Model;

/**
 * @desc Class that aggregates a Zend_View_Interface and provides
 * mutators and accessors.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class ViewAggregate implements IViewAggregate
{
    protected $_view;

    /**
     * @link IViewAggregate::getView()
     */
    public function getView()
    {
        if( null === $this->_view && \Zend_Registry::isRegistered('Zend_View') ) {
            return \Zend_Registry::get('Zend_View');
        }

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
