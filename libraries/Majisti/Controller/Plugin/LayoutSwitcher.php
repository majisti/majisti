<?php

namespace Majisti\Controller\Plugin;

/**
 * @desc The layout switcher provides the functionnality to modules
 * to have their own set of layouts, contained in a specific directory,
 * apart from the application library's layouts.
 *
 * @author Majisti
 * @license
 */
class LayoutSwitcher extends \Zend_Layout_Controller_Plugin_Layout
{
    /**
     * @desc Checks the configuration for the following keys:
     *
     * moduleName.resources.layout.layout = layoutName
     * moduleName.resources.layout.layoutPath = layoutPath [optionnal]
     *
     * where moduleName is the same module name dispatched with the request
     * where layoutName is the name of the layout script (e.g default)
     * where layoutPath is the path to the layout dir, if none is provided it
     * assumes MA_APP/application/moduleName/views/layouts
     *
     * @param Zend_Controller_Request_Abstract $request The request object
     */
    public function postDispatch(\Zend_Controller_Request_Abstract $request)
    {
        $layout     = $this->getLayout();
        $moduleName = $request->getModuleName();
        $config     = \Zend_Registry::get('Majisti_Config');

        $selector = new \Majisti\Config\Selector(
            \Zend_Registry::get('Majisti_Config'));

        /* layout name */
        $confProp = "resources.layout.{$moduleName}";

        /*
         * find layout name, and find layoutPath if it exists. If path does not
         * exists, it assumes a default dir path.
         */
        if( $layoutName = $selector->find("{$confProp}.layout", false) ) {
            $layout->setLayout($layoutName);
            $layout->setLayoutPath($config->majisti->app->path .
                    "/application/modules/{$moduleName}/views/layouts");

            if( $layoutPath = $selector->find("{$confProp}.layoutPath", false) ) {
                $layout->setLayoutPath($layoutPath);
            }
        }

        parent::postDispatch($request);
    }
}
