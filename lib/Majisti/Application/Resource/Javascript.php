<?php

namespace Majisti\Application\Resource;

/**
 * @desc Javascript resource that helps enabling various javascript lib
 * in multiple environments.
 *
 * Currently only works with jQuery and Mootools.
 *
 * Note: this was a fast implementation, in case lots of lib need
 * to be implemented, further loose coupling should be used.
 *
 * @author Majisti
 */
class Javascript extends \Zend_Application_Resource_ResourceAbstract
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
        $this->getBootstrap()->bootstrap('view');
        $this->resolveJQuery();
        $this->resolveMootools();
    }

    /**
     * @desc Resolves if jQuery should be enabled according to the options
     */
    protected function resolveJQuery()
    {
        $selector = new \Majisti\Config\Selector(new \Zend_Config($this->getOptions()));
        $settings = $this->getSettings()->majisti;
        $view     = $this->getBootstrap()->getResource('view');

        $helperPaths = $view->getHelperPaths();
        $view->setHelperPath(null);
        foreach( $helperPaths as $key => $value ) {
            $view->addHelperPath($value, $key);

            if( 'Majisti\View\Helper\\' === $key ) {
                $view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');
            }
        }

        /* jQuery and UI */
        $view->jQuery()->setLocalPath($settings->url    . '/jquery/jquery.js');
        $view->jQuery()->setUiLocalPath($settings->url  . '/jquery/jquery.ui.js');

        /* paths given, enable and set paths */
        $uiLocalPath = false;
        if( $localPath = $selector->find('jquery.path', false) ) {
            $view->jQuery()->setLocalPath($localPath);

            if( $uiLocalPath = $selector->find('jquery.ui.path', false) ) {
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

        /* add theme */
        if( $theme = $selector->find('jquery.ui.theme', false) ) {
            $file = false === strpos($theme, '.css')
                ? $settings->app->baseUrl . "/majisti/jquery/themes/{$theme}/{$theme}.css"
                : $theme;

            $view->headLink()->appendStylesheet($file);
        }
    }

    /**
     * @desc Resolves if mootools should be enabled according to the options
     */
    public function resolveMootools()
    {
        $selector = new \Majisti\Config\Selector(new \Zend_Config($this->getOptions()));
        $settings = $this->getSettings()->majisti;
        $view     = $this->getBootstrap()->getResource('view');

        $defaultPath = $settings->url . '/scripts/mootools.js';
        $path = $selector->find('mootools.path', $defaultPath);

        if( $selector->find('mootools.enable', false) )  {
            /*
             * 00 to ensure mootools is loaded first,
             * amongst keys and strings altogether, jQuery
             * will still be loaded first, because of its own
             * view helper (ZendX).
             */
            $view->headScript()->offsetSetFile('00', $path);
        }
    }
}