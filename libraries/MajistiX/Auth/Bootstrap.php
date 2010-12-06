<?php

class Auth_Bootstrap extends \Majisti\Application\ModuleBootstrap
{
    public function __construct($application)
    {
        parent::__construct($application);

        $this->loadConfiguration();
    }

    protected function loadConfiguration()
    {
        $selector = new \Majisti\Config\Selector(new Zend_Config($this->getOptions()));

        if( $useAjax = $selector->find('login.useAjax', true) ) {
            $this->bootstrap('FrontController');
            $this->bootstrap('ModelContainer');
            $container = $this->getResource('ModelContainer');

            $loginForm = $container->getModel('login',
              'Majistix_Auth_Forms', //FIXME: deprecated namespacing standard
              'MajistiX\Module\Auth\Form\Login');

            $loginForm->setUseAjax($useAjax);
        }
    }

    public function getAppNamespace()
    {
        //FIXME: abstract this
        return 'MajistiX\Module\\' . $this->getModuleName();
    }
}
