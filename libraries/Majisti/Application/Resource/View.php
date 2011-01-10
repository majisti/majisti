<?php

namespace Majisti\Application\Resource;

/**
 * @desc View resource that configures and returns an application ready
 * view that will be used by mostly the entire application.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class View extends \Zend_Application_Resource_View
{
    /**
     * @return \Zend_Config The settings needed for paths
     */
    private function getSettings()
    {
        return new \Zend_Config($this->getBootstrap()->getOptions());
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function init()
    {
        $view = $this->getView();

        $viewRenderer = new \Majisti_Controller_ActionHelper_ViewRenderer(
            $view, $this->getSettings()->toArray());
        \Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

        return $view;
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function getView()
    {
        if( null !== $this->_view ) {
            return $this->_view;
        }

        $options     = $this->getOptions();
        $view        = new \Majisti\View\View($options);
        $settings    = $this->getSettings()->majisti;

        /* majisti's base path */
        $view->addScriptPath($settings->path . '/libraries/Majisti/View/scripts');
        $view->addHelperPath('Majisti/View/Helper/', 'Majisti\View\Helper\\');
        $view->addFilterPath('Majisti/View/Filter/', 'Majisti\View\Filter\\');

        /* add all loaded extensions' base paths */
        $bootstrap = $this->getBootstrap();
        if( $bootstrap->hasPluginResource('extensions') ) {
            $bootstrap->bootstrap('extensions');
            $manager = $bootstrap->getResource('extensions');
            foreach( $manager->getLoadedExtensions() as $name => $pathInfo ) {
                $view->addBasePath("{$pathInfo['path']}/{$name}/views",
                    "{$pathInfo['namespace']}\\{$name}\View\\");
            }
        }

        /* add application's library base path */
        $view->addBasePath($settings->app->path . '/library/views',
            $settings->app->namespace . '\View\\');

        if( isset($options['doctype']) ) {
            $view->doctype(strtoupper($options['doctype']));
        } else {
            $view->doctype('XHTML1_STRICT');
        }

        \Zend_Registry::set('Zend_View', $view);

        $this->_view = $view;

        return $view;
    }

}
