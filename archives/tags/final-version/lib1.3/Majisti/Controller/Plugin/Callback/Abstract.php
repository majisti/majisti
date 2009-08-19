<?php

/**
 * TODO: doc
 *
 * @author Steven Rosato
 */
abstract class Majisti_Controller_Plugin_Callback_Abstract extends Zend_Controller_Plugin_Abstract
{
	private $_callback;
	private $_callbackContainer;
	
	public function __construct(Majisti_Callback_Container $callbackStack, Majisti_Callback_Interface $callback)
	{
		if( $callbackStack == null ) {
			throw new Majisti_Callback_Exception("The callback stack can't be null");
		}
		
		if( $callback == null ) {
			throw new Majisti_Callback_Exception("The callback can't be null");
		}
		
		$this->_callback = $callback;	
		$this->_callbackContainer = $callbackStack;
	}
	
	protected function _call(Zend_Controller_Request_Abstract $request, $params = array())
	{
		$requestSyntaxWithoutAction = $request->getModuleName() . '/' . $request->getControllerName();
		$requestSyntax = $requestSyntaxWithoutAction . '/' . $request->getActionName();
		
		if( $this->_callbackContainer->hasModule($request->getModuleName())
			&& !$this->_callbackContainer->isControllerIgnored($requestSyntaxWithoutAction)
			&& !$this->_callbackContainer->isActionIgnored($requestSyntax)
		) {
			$this->_callback->call($request, $params);
		} else if( $this->_callbackContainer->hasController($requestSyntaxWithoutAction)
			&& !$this->_callbackContainer->isActionIgnored($requestSyntax)
		) {
			$this->_callback->call($request, $params);
		} else if( $this->_callbackContainer->hasAction($requestSyntax) ) {
			$this->_callback->call($request, $params);
		}
	}
}