<?php

namespace MajistiX\Extension\Editing\Plugin;

use \Majisti\Application\Locales;

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

            if( array_search('##MAJISTIX_EDITING##', $post) ) {
                $repo = $em->getRepository(
                    'MajistiX\Extension\Editing\Model\Content');
                $model = $repo->findOrCreate(key($post), //FIXME: key/current is not adequate
                    Locales::getInstance()->getCurrentLocale());

                $em->persist($model);

                $model->setContent(current($post));
                \Zend_Debug::dump($post);
                \Zend_Debug::dump($model);

                $em->flush();

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
