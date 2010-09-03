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
        $appSettings = $this->getBootstrap()->getApplication()->getOptions();

        return new \Zend_Config($appSettings['majisti']);
    }

    /**
     * @desc Returns the configured Majisti\View
     *
     * The view will be setup with the following:
     *
     * - Majisti's view helpers added to the pluginloader
     * - MajistiX' view helpers asdded to the pluginloader
     * - ZendX JQuery view helpers added to the pluginloader
     * - Zend's static helper viewRenderer will aggregate the created view
     * - The Zend_View registry key will be setup with that view
     * - Default doctype: XHTML1_STRICT
     * - JQuery loaded according to configuration
     *
     * For more details, see the reference manual
     *
     * @return \Zend_View
     */
    public function getView()
    {
        if( null !== $this->_view ) {
            return $this->_view;
        }

        $options     = $this->getOptions();
        $view        = new \Majisti\View($options);
        $settings    = $this->getSettings();

        /* Majisti view helpers */
//        $view->addHelperPath('Majisti/View/Helper/', 'Majisti_View_Helper');
        $view->addHelperPath('Majisti/View/Helper/', 'Majisti\View\Helper\\');

        /* MajistiX view helpers */
//        $view->addHelperPath('MajistiX/View/Helper/', 'MajistiX_View_Helper');
        $view->addHelperPath('MajistiX/View/Helper/', 'MajistiX\View\Helper\\');

        /* add application's library view helpers and scripts */
        $view->addHelperPath(
            $settings->app->path      . '/library/views/helpers',
            $settings->app->namespace . '\View\Helper\\'
        );

        $view->addScriptPath($settings->app->path . '/library/views/scripts');

        /* ZendX JQuery */
        $view->addHelperPath(
            'ZendX/JQuery/View/Helper',
            'ZendX_JQuery_View_Helper'
        );

        $view->doctype('XHTML1_STRICT');

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
        $settings = $this->getSettings();

        /* jQuery and UI */
        $view->jQuery()->setLocalPath($settings->url    . '/scripts/jquery.js');
        $view->jQuery()->setUiLocalPath($settings->url  . '/scripts/jquery.ui.js');

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
