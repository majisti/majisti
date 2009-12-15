<?php

class Auth_Model_LastUrl
{
    /**
     * @var Zend_Session_Namespace
     */
    protected $_session;
    
    public function __construct(Zend_Controller_Request_Http $request = null)
    {
        $this->_session = new Zend_Session_Namespace('Majistix_Auth_LastUrl');
        $this->setRequest($request);
    }
    
    public function setRequest(Zend_Controller_Request_Http $request)
    {
        $this->_session->request = $request;
    }
    
    public function gotoLastUrl()
    {
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        
        $request = $this->_session->request;
        $redirector->gotoSimpleAndExit($request->getActionName(),
                                       $request->getControllerName(),
                                       $request->getModuleName());
    }
}
