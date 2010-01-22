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
            'Majisti/Controller/Action/Helper',
            'Majisti_Controller_Action_Helper');

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
        $view = new \Majisti\View();
        $view->addBasePath(MAJISTI_PATH . '/Layouts/');
        $view->addScriptPath(APPLICATION_LIBRARY . '/layouts');

        $view->addHelperPath('Majisti/View/Helper/', 'Majisti_View_Helper');
        $view->addHelperPath('Majisti/View/Helper/', 'Majisti\View\Helper\\');

        $view->addHelperPath('MajistiX/View/Helper/', 'MajistiX_View_Helper');
        $view->addHelperPath('MajistiX/View/Helper/', 'MajistiX\View\Helper\\');

        $view->addHelperPath(APPLICATION_LIBRARY . '/helpers', APPLICATION_NAME . '_View_Helper');
        $view->addHelperPath(APPLICATION_LIBRARY . '/helpers', APPLICATION_NAME . '\View\Helper\\');

        $view->doctype('XHTML1_STRICT');

        $view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');

        $view->jQuery()->setLocalPath(JQUERY    . '/jquery.js');
        $view->jQuery()->setUiLocalPath(JQUERY  . '/ui.js');

        //TODO: enable according to config
        $view->jQuery()->enable();

        \Zend_Registry::set('Zend_View', $view);

        return $view;
    }
}
