<?php

//namespace Majisti\View\Helper;

class Majisti_View_Helper_LayoutBuilder extends \Zend_View_Helper_Abstract
{
    public function layoutBuilder($buildable)
    {
        if( $buildable instanceof \Majisti\Util\Model\IViewAggregate ) {
            $buildable->setView($this->view);
        }
        
        return $buildable->buildAll();
    }
}