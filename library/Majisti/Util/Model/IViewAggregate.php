<?php

namespace Majisti\Util\Model;

/**
 * @desc Interface for classes that can aggregate Zend_View_Interfaces.
 * Serves as a Marker Interface at the same time.
 *  
 * @author Steven Rosato
 */
interface IViewAggregate
{
    public function getView();
    public function setView(\Zend_View_Interface $view);
}