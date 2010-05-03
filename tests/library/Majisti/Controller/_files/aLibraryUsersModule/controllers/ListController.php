<?php

#namespace aLibrary\Controllers;

class Users_ListController extends \Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_response->appendBody('aLibrary\Controllers\Users_ListController::index action was called\n');
    }    
}
