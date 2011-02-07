<?php

class AuthController extends \Zend_Controller_Action
{
    public function indexAction()
    {
        if( $this->_hasParam('login') ) {
            $auth  = \Zend_Auth::getInstance();
            $login = $this->_getParam('login');

            if( '1' === $login ) {
                $adapter = new \Zend_Auth_Adapter_Digest(
                    __DIR__ . '/../models/digest',
                    'editing',
                    'admin',
                    'admin'
                );

                $result = $auth->authenticate($adapter);
                $this->_redirect('index');
            } else {
                $auth->clearIdentity();
            }
        }
    }
}
