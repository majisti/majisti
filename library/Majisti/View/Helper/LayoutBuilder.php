<?php

//namespace Majisti\View\Helper;

class Majisti_View_Helper_LayoutBuilder extends Majisti_View_Helper_Abstract
{
    public function layoutBuilder(\Majisti\View\Layout\Builder\IBuildable $buildable)
    {
        if( $buildable instanceof \Majisti\Util\Model\Aggregator\IView ) {
            $buildable->setView($this->view);
        }

        return $buildable->buildAll();
    }
}