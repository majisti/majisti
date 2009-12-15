<?php

class Auth_IndexController extends Zend_Controller_Action
{
    /**
     * @var Zend_Auth
     */
    protected $_auth;
    
    /**
     * @var Auth_Model_LastUrl
     */
    protected $_lastUrl;
    
    /**
     * @var Zend_Controller_Action_Helper_Redirector
     */
    protected $_redirector;
    
    public function init()
    {
        $this->_auth       = Zend_Auth::getInstance();
        $this->_redirector = $this->_helper->getHelper('Redirector'); 
        
        $request = new Zend_Controller_Request_Http();
        $request->setModuleName(Zend_Controller_Front::getInstance()->getDefaultModule());
        $this->_lastUrl = new Auth_Model_LastUrl($request);
    }
    
    public function indexAction()
    {
        if( !$this->_auth->hasIdentity() ) {
            $this->_redirector->gotoSimpleAndExit('login');
        }
    }
    
    public function loginAction()
    {
        if( $this->_auth->hasIdentity() ) {
            $this->_redirector->gotoSimpleAndExit('index');
        }
        
        /**
         * TODO: change view->model to _helper->model
         * @var Zend_Form $form
         */
        $form   = $this->view->model('login', 'Majistix_Users_Forms', 'Auth_Form_Login');
        $req    = $this->_request;
        
        /* login request */
        if( $req->isPost() && $form->isValid($req->getPost()) ) {
            $adapter    = $this->_getAdapter($req->getPost());
            $result     = $this->_auth->authenticate($adapter);

            if( $result->getCode() === Zend_Auth_Result::SUCCESS ) {
                $this->_storeData($adapter);
                $this->_lastUrl->gotoLastUrl();
            }
        }
    }
    
    protected function _getAdapter($postData)
    {
        return new Zend_Auth_Adapter_Digest(
            dirname(__FILE__) . '/../models/identities.txt',
            'realm',
            $postData['login'],
            $postData['pass']
        );
    }
    
    protected function _storeData(Zend_Auth_Adapter_Interface $adapter)
    {
        $this->_auth->getStorage()->write(array(
            'realm'     => $adapter->getRealm(),
            'username'  => $adapter->getUsername(),
            'password'  => $adapter->getPassword()
        ));
    }
}