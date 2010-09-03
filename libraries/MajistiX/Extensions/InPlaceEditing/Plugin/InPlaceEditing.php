<?php

namespace MajistiX\Controller\Plugin;

/**
 * @desc InPlaceEditing controller plugin that listens for a special
 * InPlaceEditing post that will be inserted in the current InPlaceEditing
 * model from the static model container. It supports either normal post
 * or xml http request and sends a response for the latter.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
use Majisti\Controller\Plugin;

class InPlaceEditing extends \Majisti\Controller\Plugin\AbstractPlugin
{
    /**
     * @desc On InPlaceEditing post, update or insert provided post value
     * for provided post key.
     *
     * @param $request The request
     */
    public function postDispatch(\Zend_Controller_Request_Abstract $request)
    {
        //TODO: according to config, if in-place-editing is enabled per ACL, check acl first

        if( $request->isPost() ) {
            $post = $request->getPost();
            if( array_search('##MAJISTI_INPLACE_EDITING##', $post) ) {
                $editor = \Zend_Registry::get('Majisti_ModelContainer')
                    ->getModel('model', 'MajistiX_InPlaceEditing');

                //FIXME: if other elements are posted, key/value may not be the first
                $editor->editContent(key($post), current($post));

                if( $request->isXmlHttpRequest() ) {
                    //TODO: send response success
                } else {
                    header('Location: ' . $this->getView()->url());
                }
                return;
            }
        }
    }
}
