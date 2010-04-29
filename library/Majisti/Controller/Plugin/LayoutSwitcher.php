<?php

namespace Majisti\Controller\Plugin;

class LayoutSwitcher extends \Zend_Layout_Controller_Plugin_Layout
{
    public function preDispatch(\Zend_Controller_Request_Abstract $request)
    {
        $layout     = $this->getLayout();
        $layoutPath = $layout->getLayoutPath();
        $moduleName = $request->getModuleName();

        $selector = new \Majisti\Config\Selector(
            \Zend_Registry::get('Majisti_Config'));

        $confProp = "{$moduleName}.resources.layout";

        if( $moduleName = $selector->find("{$confProp}.layout", false) ) {
            $layout->setLayout($moduleName);
            $layout->setLayoutPath(APPLICATION_PATH . "/modules/{$moduleName}/views/layouts");

            if( $moduleLayoutPath = $selector->find("{$confProp}.layoutPath", false) ) {
                $layout->setLayoutPath($moduleLayoutPath);
            }
        }
    }
}
