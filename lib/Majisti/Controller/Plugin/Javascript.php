<?php

namespace Majisti\Controller\Plugin;

use Zend_Controller_Request_Abstract as Request;

/**
 * @desc Javascript plugin that will transform needed configuration a js
 * object. Useful for dynamic configuration.
 *
 * @author Majisti
 */
class Javascript extends AbstractPlugin
{
    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function preDispatch(Request $request)
    {
        $this->initJavascript($request);
    }

    /**
     * @desc Transform the config into a js object.
     *
     * @param Request $request The request
     */
    protected function initJavascript(Request $request)
    {
        $view   = $this->getView();
        $config = $this->getConfig()->majisti;

        $conf = \Zend_Json::encode(array(
            'app'  => $config->app->toArray()
                      + array('currentUrl' => $view->url()),
            'url'  => $config->url,
            'path' => $config->path,
            'ext'  => new \ArrayObject(),
        ));

        if( !('production' === $config->app->env && 'staging' === $config->app->env) ) {
            $conf = \Zend_Json::prettyPrint($conf, array('indent' => '  '));
        }

        $view->headScript()->prependScript("var majisti = {$conf};");
    }
}
