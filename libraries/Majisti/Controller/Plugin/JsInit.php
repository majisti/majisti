<?php

namespace Majisti\Controller\Plugin;

class JsInit extends AbstractPlugin
{
    public function preDispatch(\Zend_Controller_Request_Abstract $request)
    {
        $view   = $this->getView();
        $config = $this->getConfig()->majisti;

        $conf = \Zend_Json::encode(array(
            'app'  => $config->app->toArray()
                      + array('currentUrl' => $view->url()),
            'url'  => $config->url,
            'path' => $config->path,
            'ext'  => array(),
        ));

        if( !('production' === $config->app->env && 'staging' === $config->app->env) ) {
            $conf = \Zend_Json::prettyPrint($conf, array('indent' => '  '));
        }

        $view->headScript()->prependScript("var majisti = {$conf};");
    }
}