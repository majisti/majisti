<?php

class Majisti_View_Helper_Model extends HelperAbstract
{
    public function model($key, $namespace = 'default',
        $returnModel = null, array $args = array())
    {
        $modelContainer = \Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')
            ->getResource('ModelContainer');

        return $modelContainer->getModel($key, $namespace, $returnModel, $args);
    }
}
