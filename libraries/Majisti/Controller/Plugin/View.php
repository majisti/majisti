<?php

namespace Majisti\Controller\Plugin;

use Zend_Controller_Request_Abstract as Request;

/**
 * @desc View plugin that will enabled view helpers for application modules.
 *
 * @author Majisti
 */
class View extends AbstractPlugin
{
    /*
     * (non-phpDoc) 
     * @see Inherited documentation.
     */
    public function preDispatch(Request $request)
    {
        $this->initViewHelpers($request);
    }

    /**
     * @desc Inits the view helpers for application modules.
     *
     * @param Request $request The request
     */
    public function initViewHelpers(Request $request)
    {
        $view   = $this->getView();
        $module = $request->getModuleName();
        $app    = $this->getConfig()->majisti->app;

        $path = $app->path . "/application/modules/{$module}/views/helpers";
        $ns   = $app->namespace . '\\' . ucfirst($module) . '\View\Helper\\';
        $view->addHelperPath($path, $ns);
    }
}
