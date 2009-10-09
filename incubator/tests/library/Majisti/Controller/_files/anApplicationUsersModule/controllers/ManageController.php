<?php

namespace anApplication\Controllers;

require_once dirname(__FILE__) . '/../../aLibraryUsersModule/controllers/ManageController.php';

class Users_ManageController extends \aLibrary\Controllers\Users_ManageController
{
    public function indexAction()
    {
        $this->_response->appendBody(__NAMESPACE__ . '\Users_ManageController::index action was called\n');
    }
}
