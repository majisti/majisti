<?php

namespace Majisti\Controller\Plugin;

class JsInit extends AbstractPlugin
{
    public function preDispatch(\Zend_Controller_Request_Abstract $request)
    {
        $view = $this->getView();

        $js = \Zend_Json::encode(array(
            'appUrl'     => APPLICATION_URL,
            'baseUrl'    => BASE_URL,
            'currentUrl' => $view->url(),
        ));

        $view->headScript()->prependScript("majisti_init({$js});");
        $view->headScript()->prependFile(MAJISTI_URL_SCRIPTS . '/init.js');
    }
}