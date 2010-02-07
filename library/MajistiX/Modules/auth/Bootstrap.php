<?php

class Auth_Bootstrap extends \Majisti\Application\ModuleBootstrap
{
    public function __construct($application)
    {
        parent::__construct($application);

        $this->_loadConfiguration();
    }

    protected function _loadConfiguration()
    {
        $selector = new \Majisti\Config\Selector(new Zend_Config($this->getOptions()));

        if( $useAjax = $selector->find('login.useAjax', true) ) {
            $this->bootstrap('FrontController');
            $this->bootstrap('ModelContainer');
            $container = $this->getResource('ModelContainer');

            $loginForm = $container->getModel('login',
                                              'Majistix_Auth_Forms',
                                              'Auth_Form_Login');

            $loginForm->setUseAjax($useAjax);
        }
    }
}
