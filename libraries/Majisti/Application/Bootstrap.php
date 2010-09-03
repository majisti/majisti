<?php

namespace Majisti\Application;

/**
 * @desc Majisti's application boostrap.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Bootstrap extends \Zend_Application_Bootstrap_Bootstrap
{
    /**
     * @desc Inits the application's library autoloader
     * which is basically the same as a module autoloader.
     *
     * @return \Zend_Application_Module_Autoloader
     */
    protected function _initLibraryAutoloader()
    {
        $options = $this->getApplication()->getOptions();

        return new \Zend_Application_Module_Autoloader(array(
            'basePath'  => $options['majisti']['app']['path'] . '/library',
            'namespace' => $options['majisti']['app']['namespace'],
        ));
    }

    /**
     * @desc Inits the action helper broker
     */
    protected function _initActionHelper()
    {
        \Zend_Controller_Action_HelperBroker::addPath(
            'Majisti/Controller/ActionHelper',
            'Majisti_Controller_ActionHelper');
    }
}
