<?php

require_once __DIR__ . '/../../aLibraryUsersModule/controllers/ManageController.php';

class Users_ManageController extends \aLibrary\Controllers\Users_ManageController
{
    public function indexAction()
    {
        $this->_response->appendBody('Users_ManageController::index
            action was called\n');
    }
}
