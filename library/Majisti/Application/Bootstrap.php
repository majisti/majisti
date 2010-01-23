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
     * which is basically the same a an module autoloader.
     *
     * @return \Zend_Application_Module_Autoloader
     */
    protected function _initLibraryAutoloader()
    {
        $autoloader = \Zend_Loader_Autoloader::getInstance();
        $libAutoloader = new \Zend_Application_Module_Autoloader(array(
            'basePath'  => APPLICATION_LIBRARY,
            'namespace' => APPLICATION_NAME
        ));

        $autoloader->pushAutoloader($libAutoloader);

        return $libAutoloader;
    }
}
