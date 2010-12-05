<?php

class Auth_View_Helper_IdentityPanel extends Zend_View_Helper_Abstract
{
    public function identityPanel()
    {
        $auth = Zend_Auth::getInstance();

        return $auth->hasIdentity()
                ? 'Logged'
                : 'Not logged';
    }
}