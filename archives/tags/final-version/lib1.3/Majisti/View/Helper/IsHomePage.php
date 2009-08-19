<?php

class Majisti_View_Helper_IsHomePage extends Zend_View_Helper_Abstract
{
	public function isHomePage($moduleName = 'default', $controllerName = 'index', $actionName = 'index')
	{
		$request = Zend_Controller_Front::getInstance()->getRequest();
		
		if( $request->getModuleName() == $moduleName 
			&& $request->getControllerName() == $controllerName
			&& $request->getActionName() == $actionName 
		) {
			return true;
		}
		
		return false;
	}
}