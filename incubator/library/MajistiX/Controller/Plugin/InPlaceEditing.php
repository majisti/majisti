<?php

namespace MajistiX\Controller\Plugin;

class InPlaceEditing extends AbstractPlugin
{
    public function postDispatch(\Zend_Controller_Request_Abstract $request)
    {
        $view = \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
        $view->headScript()->appendFile(MAJISTI_URL . '/majistix/scripts/ckeditor/ckeditor.js');
        
        //TODO: according to config, if in-place-editing is enabled per acl, check acl first
        
        if( $request->isPost() ) {
            $post = $request->getPost();
            if( array_search('##MAJISTI_INPLACE_EDITING##', $post) ) {
                //FIXME: should be retrieved via model container
                $editor = \Zend_Registry::get('Majisti_InPlaceEditing_Model');
                
                //FIXME: if other elements are posted, key/value may not be the first
                $editor->editContent(key($post), current($post));
                
                if( $request->isXmlHttpRequest() ) {
                    //TODO: send response success
                } else {
                    header('Location: ' . $view->url());
                }
                return;
            }
        }
    }    
}
