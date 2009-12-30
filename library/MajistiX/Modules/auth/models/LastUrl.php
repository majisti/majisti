<?php

/**
 * @desc Model that will remember an url through the session
 * 
 * @author Steven Rosato
 */
class Auth_Model_LastUrl
{
    /**
     * @var Zend_Session_Namespace
     */
    protected $_session;
    
    /**
     * @desc Constructs the model with a request object.
     * 
     * @param $request The request
     */
    public function __construct(Zend_Controller_Request_Http $request = null)
    {
        $this->_session = new Zend_Session_Namespace('Majistix_Auth_LastUrl');
        $this->setRequest($request);
    }
    
    /**
     * @desc Returns the request
     * 
     * @return Zend_Controller_Request_Http The request
     */
    public function getRequest()
    {
        return $this->_session->request;
    }
    
    /**
     * @desc Sets the request object.
     * @param $request The request object
     */
    public function setRequest(Zend_Controller_Request_Http $request)
    {
        $this->_session->request = $request;
    }
    
    /**
     * @desc Redirect according to the request's setup action, controller
     * and module names.
     */
    public function gotoLastUrl()
    {
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        
        $request = $this->_session->request;
        $redirector->gotoSimpleAndExit($request->getActionName(),
                                       $request->getControllerName(),
                                       $request->getModuleName());
    }
}
