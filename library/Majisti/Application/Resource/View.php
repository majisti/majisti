<?php

namespace Majisti\Application\Resource;

/**
 * @desc View resource that configurates and returns an application ready
 * view that will be used by mostly the entire application.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class View extends \Zend_Application_Resource_View
{
    /**
     * @desc Inits the view
     */
    public function init()
    {
        //FIXME: does it have anything to do here?
        \Zend_Controller_Action_HelperBroker::addPath(
            'Majisti/Controller/ActionHelper',
            'Majisti_Controller_ActionHelper');

        return parent::init();
    }

    /**
     * @desc Returns the configured Majisti\View
     *
     * The view will be setup with the following:
     *
     * - Majisti's Layout basebath is setup for abstract layouts
     * - Majisti's view helpers added to the pluginloader
     * - Majisti's action helpers added to the Zend_Controller_Action HelperBroker
     * - ZendX JQuery view helpers added to the pluginloader
     * - Zend's static helper viewRenderer will aggregate the created view
     *
     * - The Zend_View registry key will be setup with that view
     *
     * @return \Majisti\View
     */
    public function getView()
    {
        if( null !== $this->_view ) {
            return $this->_view;
        }

        $options = $this->getOptions();

        $view = new \Majisti\View();
//        $view->addBasePath(MAJISTI_PATH . '/Layouts/');
        $view->addScriptPath(APPLICATION_LIBRARY . '/views/scripts');

        $view->addHelperPath('Majisti/View/Helper/', 'Majisti_View_Helper');
        $view->addHelperPath('Majisti/View/Helper/', 'Majisti\View\Helper\\');

        $view->addHelperPath('MajistiX/View/Helper/', 'MajistiX_View_Helper');
        $view->addHelperPath('MajistiX/View/Helper/', 'MajistiX\View\Helper\\');

        $view->addHelperPath(APPLICATION_LIBRARY . '/views/helpers', APPLICATION_NAME . '_View_Helper');
        $view->addHelperPath(APPLICATION_LIBRARY . '/views/helpers', APPLICATION_NAME . '\View\Helper\\');

        $view->doctype('XHTML1_STRICT');

        $view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');

        if( isset($options['google']) ) {
            $this->loadGoogle($view, $options['google']);
        }

        $view->jQuery()->setLocalPath(JQUERY    . '/jquery.js');
        $view->jQuery()->setUiLocalPath(JQUERY  . '/ui.js');

        //TODO: enable according to config
        $view->jQuery()->enable();
        $view->jQuery()->uiEnable();

        \Zend_Registry::set('Zend_View', $view);

        $this->_view = $view;

        return $view;
    }

    protected function loadGoogle($view, $google)
    {
        $key = $google['apikey'];
        $view->headScript()->prependFile("http://www.google.com/jsapi?key={$key}");

        $load = '';
        foreach ($google['load'] as $key => $value) {

            /* disable jquery and ui library loading */
            if( 'jquery' === strtolower($key) ) {
                $view->jQuery()->setRenderMode(30);
            }

        	$script = 'google.load("' . $key . '"';

        	foreach ($value as $val) {
        		$script .= ', ' . $val . '';
        	}
        	$load .= $script . ');' . PHP_EOL;
        }

        $view->headScript()->appendScript($load);
    }
}
