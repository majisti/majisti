<?php

/**
 * @desc The authenfication main controller will process any form
 * of login through a simple interface.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
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

    /**
     * @var Zend_Auth_Adapter_Interface
     */
    protected $_authAdapter;

    /**
     * @desc Inits the controller by remembering the success redirection url
     * provided [optionnaly] through request parameters.
     */
    public function init()
    {
        $this->_auth       = Zend_Auth::getInstance();
        $this->_redirector = $this->_helper->getHelper('Redirector');

        //TODO: fetch redirection URL from request

        $request = new Zend_Controller_Request_Http();
        $request->setModuleName(Zend_Controller_Front::getInstance()
            ->getDefaultModule());
        $this->_lastUrl = new Auth_Model_LastUrl($request);
    }

    /**
     * @desc Authentification index
     */
    public function indexAction()
    {
        if( !$this->_auth->hasIdentity() ) {
            $this->_redirector->gotoSimpleAndExit('login');
        }
    }

    /**
     * @desc Login action where post data is handled through either simple post
     * or ajax post. The form model will render appropriate errors
     * depending on the authentification failure or form filling errors.
     * When the authentification is done successfully through its adapter,
     * either a redirection or response will be sent.
     */
    public function loginAction()
    {
        if( $this->_auth->hasIdentity() ) {
            $this->_redirector->gotoSimpleAndExit('index');
        }

        /**
         * TODO: change view->model to _helper->model
         * @var Zend_Form $form
         */
        $form = $this->view->model('login', 'Majistix_Auth_Forms');

        $req  = $this->_request;

        if( $req->isPost() && $form->isValid($req->getPost()) ) {
            $this->_adapter = $this->_createAdapter($req->getPost());
            $result         = $this->_auth->authenticate($this->_adapter);

            /* auth success */
            if( $result->isValid() ) {
                $this->_storeData();

                if( $req->isXmlHttpRequest() ) {
                    $this->render('index');
                    $this->_response->sendResponse();
                    exit;
                }

                $this->_lastUrl->gotoLastUrl();
            } else { /* append result's error message to the form */
                $form->addDecorator('Errors');
                $form->addErrors($result->getMessages());
            }
        }

        /* send response on ajax request */
        if( $req->isXmlHttpRequest() ) {
            $this->_response->setBody($form->render())->sendResponse();
            exit;
        }
    }

    /**
     * @desc Clears indentity
     * TODO: redirect from referer instead
     */
    public function logoutAction()
    {
        \Zend_Session::forgetMe();
        $this->_auth->clearIdentity();

        $this->_redirector->gotoSimpleAndExit('index', 'index', 'auth');
    }

    /**
     * @desc Returns the authentification adapter for this authentification
     * module.
     *
     * TODO: review the default identities file loading directory,
     * should it be fetched via a configuration model?
     *
     * @param array $postData The post data
     *
     * @return Zend_Auth_Adapter_Interface The adapter
     */
    protected function _createAdapter($postData)
    {
        return new Zend_Auth_Adapter_Digest(
            dirname(__FILE__) . '/../models/identities.txt',
            'realm',
            $postData['login'],
            $postData['pass']
        );
    }

    /**
     * @desc Stores the data in the authentifaction storage.
     */
    protected function _storeData()
    {
        $adapter = $this->_adapter;

        $this->_auth->getStorage()->write(array(
            'realm'     => $adapter->getRealm(),
            'username'  => $adapter->getUsername(),
            'password'  => $adapter->getPassword()
        ));
    }
}