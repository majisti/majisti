<?php

namespace Majisti\Controller\Plugin;

class JsInit extends AbstractPlugin
{
    public function preDispatch(\Zend_Controller_Request_Abstract $request)
    {
        $view   = $this->getView();
        $config = $this->getConfig();

        $js = \Zend_Json::encode(array(
            'appUrl'     => $config->majisti->application->url,
            'baseUrl'    => $config->majisti->application->baseUrl,
            'currentUrl' => $view->url(),
        ));

        $view->headScript()->prependScript("majisti_init({$js});");
        $view->headScript()->prependFile($config->majisti->url. '/scripts/init.js');
    }
}