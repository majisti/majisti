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

        /* ZendX JQuery */
        $view->addHelperPath(
            'ZendX/JQuery/View/Helper',
            'ZendX_JQuery_View_Helper'
        );

        /* majisti's base path */
        $view->addScriptPath($settings->path . '/libraries/Majisti/View/scripts');
        $view->addHelperPath('Majisti/View/Helper/', 'Majisti\View\Helper\\');
        $view->addFilterPath('Majisti/View/Filter/', 'Majisti\View\Filter\\');

        /* add all loaded extensions' base paths */
        $this->getBootstrap()->bootstrap('addons');
        $manager = $this->getBootstrap()->getResource('addons');
        foreach( $manager->getLoadedExtensions() as $name => $pathInfo ) {
            $view->addBasePath("{$pathInfo['path']}/{$name}/views",
                "{$pathInfo['namespace']}\Extension\\{$name}\View\\");
        }

        /* add application's library base path */
        $view->addBasePath($settings->app->path . '/library/views',
            $settings->app->namespace . '\View\\');

        if( isset($options['doctype']) ) {
            $view->doctype(strtoupper($options['doctype']));
        } else {
            $view->doctype('XHTML1_STRICT');
        }

        $this->resolveJQuery($view, $options);

        \Zend_Registry::set('Zend_View', $view);

        $this->_view = $view;

        return $view;
    }

    /**
     * @desc Resolves if jQuery should be enabled according to the options
     *
     * @param View $view The view
     * @param Array $options The options
     */
    protected function resolveJQuery($view, $options)
    {
        $selector = new \Majisti\Config\Selector(new \Zend_Config($options));
        $settings = $this->getSettings()->majisti;

        /* jQuery and UI */
        $view->jQuery()->setLocalPath($settings->url    . '/jquery/jquery.js');
        $view->jQuery()->setUiLocalPath($settings->url  . '/jquery/jquery.ui.js');

        /* paths given, enable and set paths */
        $uiLocalPath = false;
        if( $localPath = $selector->find('jquery.localPath', false) ) {
            $view->jQuery()->setLocalPath($localPath);

            if( $uiLocalPath = $selector->find('jquery.ui.localPath', false) ) {
                $view->jQuery()->setUiLocalPath($uiLocalPath);
            }
        }

        /*
         * Enable when a local path is specified but jQuery is not explicitely
         * enabled or enable when jQuery is explicitely enabled but no path was
         * specified.
         */
        $enabled    = $selector->find('jquery.enable', null);
        $uiEnabled  = $selector->find('jquery.ui.enable', null);

        /* null means the selection was not found */
        if( ($localPath && null === $enabled) || $enabled) {
            $view->jQuery()->enable();
        }

        if( ($uiLocalPath && null === $uiEnabled) || $uiEnabled) {
            $view->jQuery()->uiEnable();
        }
    }
}
