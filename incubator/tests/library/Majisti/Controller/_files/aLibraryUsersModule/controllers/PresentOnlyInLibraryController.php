<?php

namespace aLibrary\Controllers;

class Users_PresentOnlyInLibraryController extends \Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_response->appendBody('aLibrary\Controllers\Users_PresentOnlyInLibrary::index was called \n');
    }
}
