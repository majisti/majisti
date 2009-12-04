<?php

class Majisti_View_Helper_Model extends Zend_View_Helper_Abstract
{
    public function model($key)
    {
        $modelContainer = \Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')
            ->getResource('ModelContainer');
        
        return $modelContainer->getModel($key);
    }    
}
