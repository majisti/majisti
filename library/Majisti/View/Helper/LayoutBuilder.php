<?php

//namespace Majisti\View\Helper;

class Majisti_View_Helper_LayoutBuilder extends HelperAbstract
{
    public function layoutBuilder(\Majisti\View\Layout\Builder\IBuildable $buildable)
    {
        if( $buildable instanceof \Majisti\Util\Model\IViewAggregate ) {
            $buildable->setView($this->view);
        }

        return $buildable->buildAll();
    }
}