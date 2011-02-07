<?php

/**
 * @desc The main's module bootstrap.
 *
 * @author Steven Rosato
 */
class Main_Bootstrap extends \Majisti\Application\ModuleBootstrap
{
    protected function _initAuth()
    {
        \Zend_Auth::getInstance()->setStorage(
            new \Zend_Auth_Storage_Session('MajistiX_Editing')
        );
    }
}
