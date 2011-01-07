<?php

namespace MajistiX\Editing\Plugin;

use \Majisti\Application\Locales,
    \Zend_Controller_Action_HelperBroker as HelperBroker;

/**
 * @desc InPlaceEditing controller plugin that listens for a special
 * InPlaceEditing post that will be inserted in the current InPlaceEditing
 * model from the static model container. It supports either normal post
 * or xml http request and sends a response for the latter.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class ContentMonitor extends \Majisti\Controller\Plugin\AbstractPlugin
{
    /**
     * @desc On InPlaceEditing post, update or insert provided post value
     * for provided post key.
     *
     * @param $request The request
     */
    public function postDispatch(\Zend_Controller_Request_Abstract $request)
    {
        if( $request->isPost() ) {
            $post = $request->getPost();
            /* @var $em \Doctrine\ORM\EntityManager */
            $em   = \Zend_Registry::get('Doctrine_EntityManager');

            if( $key = array_search('##MAJISTIX_EDITING##', $post) ) {

                $key = str_replace('maj_editing_editor_hidden_', '', $key);

                $repo = $em->getRepository(
                    'MajistiX\Editing\Model\Content');
                $model = $repo->findOrCreate($key,
                    Locales::getInstance()->getCurrentLocale());

                $em->persist($model);

                $model->setContent($post[$key]);

                $em->flush();

                if( $request->isXmlHttpRequest() ) {
                    /* @var $json \Zend_Controller_Action_Helper_Json */
                    $json = HelperBroker::getStaticHelper('json');
                    $json->direct(array(
                        'result'  => 'success',
                        'message' => 'Content successfully updated.'
                    ));
                } else {
                    /* @var $redirector \Zend_Controller_Action_Helper_Redirector */
                    $redirector = HelperBroker::getStaticHelper('redirector');
                    $redirector->gotoSimple(
                        $request->getActionName(),
                        $request->getControllerName(),
                        $request->getModuleName()
                    );
                }
            }
        }
    }
}
