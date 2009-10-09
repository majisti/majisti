<?php

namespace MajistiX\Controller\Plugin;

class CkEditor extends AbstractPlugin
{
    public function preDispatch(\Zend_Controller_Request_Abstract $request)
    {
        $view = \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
        $view->headScript()->appendFile(MAJISTI_URL . '/majistix/scripts/ckeditor/ckeditor.js');
    }    
}
