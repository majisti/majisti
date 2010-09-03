<?php

namespace aLibrary\Controllers;

class Users_ManageController extends \Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_response->appendBody('aLibrary\Controllers\Users_ManageController::index action was called \n');
    }
}
