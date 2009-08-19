<?php

/**
 * TODO: doc
 *
 * @author Steven Rosato
 */
class Majisti_Controller_Action_Helper_TemplateForward extends Zend_Controller_Action_Helper_Abstract
{
	private static $_requests;
	
	private static $_registerToView = false;
	private static $_storeToSession = true;
	
	public function direct($action, $controller = null, $module = null, array $params = null)
	{
		$this->forward($action, $controller, $module, $params);
	}
	
	public function forward($action, $controller = null, $module = null, array $params = null)
	{
		$this->registerRequestNames();
		
		$request = $this->getRequest();

        if (null !== $params) {
            $request->setParams($params);
        }

        if (null !== $controller) {
            $request->setControllerName($controller);

            if (null !== $module) {
                $request->setModuleName($module);
            }
        }

        $request->setActionName($action)
                ->setDispatched(false);
	}
	
	public function ignoreForward($ignoredViews = array(), $action, $controller = null, $module = null, array $params = null)
	{
		$request = $this->getRequest();

		if( is_array($ignoredViews) ) {
			foreach ($ignoredViews as $view) {
				if( $view == $request->getActionName() ) { return; }
			}
		} else {
			if( $ignoredViews == $request->getActionName() ) { return; }
		}
		
		$this->forward($action, $controller, $module, $params);
	}
	
	public function toggleRegisterToView()
	{
		self::$_registerToView = !self::$_registerToView;
		
		return $this;
	}
	
	public function toggleStoreToSession()
	{
		self::$_storeToSession = !self::$_storeToSession;
		
		return $this;
	}
	
	private function _store()
	{
		$session = new Zend_Session_Namespace('Majisti_Controller_Action_Helper_TemplateForward');
		$session->requests = self::$_requests;
	}
	
	public function registerRequestNames()
	{
		$controller = $this->getActionController();
		$request 	= $controller->getRequest();
		
		if( $request->getActionName() == 'images' ) {
			
			exit;
		}
		
		self::$_requests = array();
		self::$_requests['lastModuleName'] 		= $request->getModuleName();
		self::$_requests['lastControllerName'] 	= $request->getControllerName();
		self::$_requests['lastActionName'] 		= $request->getActionName();
		
		if( self::$_registerToView ) {
			$this->registerLastRequestsNamesToView();
		}
		
		if( self::$_storeToSession ) {
			$this->_store();
		}
		
		return $this;
	}
	
	public function registerLastRequestsNamesToView($clearRequests = false)
	{
		$controller = $this->getActionController();
		
		$controller->view->lastModuleName 		= $this->getLastModuleName();
		$controller->view->lastControllerName 	= $this->getLastControllerName();
		$controller->view->lastActionName 		= $this->getLastActionName();
		
		if( $clearRequests ) {
			$this->clearRequests();
		}
		
		return $this;
	}
	
	private function _retrieveRequest($key)
	{
		if( self::$_requests == null ) {
			$session = new Zend_Session_Namespace('Majisti_Controller_Action_Helper_TemplateForward');
			if( isset($session->requests) ) {
				self::$_requests = $session->requests;
			}
		}
		
		if( self::$_requests != null ) {
			if( array_key_exists($key, self::$_requests) ) {
				return self::$_requests[$key];
			}
		}
	}
	
	public function clearRequests($clearInSession = true)
	{
		$session = new Zend_Session_Namespace('Majisti_Controller_Action_Helper_TemplateForward');
		$session->unsetAll();
		
		self::$_requests = null;
		
		return $this;
	}
	
	public function getLastModuleName()
	{
		return $this->_retrieveRequest('lastModuleName');
	}
	
	public function getLastControllerName()
	{
		return $this->_retrieveRequest('lastControllerName');
	}
	
	public function getLastActionName()
	{
		return $this->_retrieveRequest('lastActionName');
	}
}