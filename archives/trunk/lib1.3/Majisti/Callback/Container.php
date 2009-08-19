<?php

/**
 * @desc This class provides a reside point for modules, controllers and action names. It is read-only.
 * This is mainly used is conjunction with Majisti_Callback_Interface and 
 * Majisti_Controller_Plugin_Callback_Abstract to append specific callback code to a list
 * of modules, controllers and actions. 
 * 
 * It is also possible, while having modules, controllers or actions in the container,
 * to define ignored controllers and actions as well. It means that a controller or action
 * added in the container will not be returned as true if it was added as ignored as well.
 * 
 * The 'moduleName' should only have the request module's name syntax
 * 
 * The 'controllerName' must follow this very specific syntax:
 * 
 * moduleName/controllerName
 * 
 * The 'actionName' must follow this very specific syntax:
 * 
 * moduleName/controllerName/actionName
 * 
 * @see Majisti_Callback_Interface, Majisti_Controller_Plugin_Callback_Abstract
 * @author Steven Rosato
 * @version 1.0
 */
final class Majisti_Callback_Container
{
	private $_modules 				= array();
	
	private $_controllers 			= array();
	private $_ignoredControllers 	= array();
	
	private $_actions				= array();
	private $_ignoredActions		= array();
	
	const MODULE 		= 1;
	const CONTROLLER 	= 2;
	const ACTION 		= 3;
	
	/**
	 * This will add a module to the container retrievable later on with
	 * hasModule.
	 *
	 * @param string|array $modulesNames
	 */
	public function addModule($modulesNames)
	{
		if( is_array($modulesNames) ) {
			foreach ($modulesNames as $moduleName) {
				array_push($this->_modules, $moduleName);
			}
		} else {
			array_push($this->_modules, $modulesNames);
		}
	}
		
	/**
	 * @desc Add a controller to the container. This specific syntax mut be followed for the string:
	 * 
	 * moduleName/controllerName
	 *
	 * @param string|array $controllers The specific syntax following moduleName/controllerName
	 */
	public function addController($controllers)
	{
		if( is_array($controllers) ) {
			foreach ($controllers as $controller) {
				$this->_addRequestTo($this->_controllers, self::CONTROLLER, $controller);
			}
		} else {
			$this->_addRequestTo($this->_controllers, self::CONTROLLER, $controllers);
		}
	}
	
	/**
	 * @desc Add an action to the container. This specific syntax mut be followed for the string:
	 * 
	 * moduleName/controllerName/actionName
	 *
	 * @param string|array $actions The specific syntax following moduleName/controllerName/actionName
	 */
	public function addAction($actions)
	{
		if( is_array($actions) ) {
			foreach ($actions as $action) {
				$this->_addRequestTo($this->_actions, self::ACTION, $action);
			}
		} else {
			$this->_addRequestTo($this->_actions, self::ACTION, $actions);
		}
	}
	
	/**
	 * @desc Add an ignored controller to the container. This specific syntax mut be followed for the string:
	 * 
	 * moduleName/controllerName
	 * 
	 * @param string|array $controllers The specific syntax following moduleName/controllerName
	 */
	public function ignoreController($controllers)
	{
		if( is_array($controllers) ) {
			foreach ($controllers as $controller) {
				$this->_addRequestTo($this->_ignoredControllers, self::CONTROLLER, $controller);
			}
		} else {
			$this->_addRequestTo($this->_ignoredControllers, self::CONTROLLER, $controllers);
		}
	}
	
	/**
	 * @desc Add an ignored action to the container. This specific syntax mut be followed for the string:
	 * 
	 * moduleName/controllerName/actionName
	 * 
	 * @param string|array $actions The specific syntax following moduleName/controllerName/actionName
	 */
	public function ignoreAction($actions)
	{
		if( is_array($actions) ) {
			foreach ($actions as $action) {
				$this->_addRequestTo($this->_ignoredActions, self::ACTION, $action);
			}
		} else {
			$this->_addRequestTo($this->_ignoredActions, self::ACTION, $actions);
		}
	}
	
	/**
	 * Returns whether the moduleName given as parameter is contained in this container.
	 *
	 * @param string|array $modulesNames The module name
	 * @return bool if the module is contained
	 */
	public function hasModule($modulesNames)
	{
		if( is_array($modulesNames) ) {
			foreach ($modulesNames as $moduleName) {
				if( in_array($moduleName, $this->_modules) ) {
					return true;
				}
			}
		} else {
			return in_array($modulesNames, $this->_modules);
		}
	}
	
	/**
	 * @desc Returns whether the controller with the above syntax given as parameter is contained in this container.
	 * If the controller is contained, but was ignored this method will return false.
	 * 
	 * Syntax: moduleName/controllerName
	 * 
	 * @param string $controllers With the specific syntax
	 * @return bool true if the controller with the specific syntax is contained but not ignored
	 */
	public function hasController($controllers)
	{
		if( is_array($controllers) ) {
			foreach ($controllers as $controller) {
				if( $this->_hasRequestIn(self::CONTROLLER, $controller) && !$this->isControllerIgnored($controller) ) {
					return true;
				}
			}
		} else {
			return $this->_hasRequestIn(self::CONTROLLER, $controllers) && !$this->isControllerIgnored($controllers);
		}
	}
		
	/**
	 * @desc Returns whether the action with the above syntax given as parameter is contained in this container.
	 * If the action is contained, but was ignored this method will return false.
	 * 
	 * Syntax: moduleName/controllerName/actionName
	 * 
	 * @param string|array $actions With the specific syntax
	 * @return bool true if the action with the specific syntax is contained but not ignored
	 */
	public function hasAction($actions)
	{
		if( is_array($actions) ) {
			foreach ($actions as $action) {
				if( $this->_hasRequestIn(self::ACTION, $action) && !$this->isActionIgnored($action) ) {
					return true;
				}
			}
		} else {
			return $this->_hasRequestIn(self::ACTION, $actions) && !$this->isActionIgnored($actions);
		}
	}
		
	/**
	 * @desc Returns whether the controller with the above syntax given as parameter is ignored in this container.
	 * If the controller is ignored, it returns true.
	 * 
	 * Syntax: moduleName/controllerName
	 * 
	 * @param string|array $controllers With the specific syntax
	 * @return bool true if the controller with the specific syntax is ignored
	 */
	public function isControllerIgnored($controllers)
	{
		if( is_array($controllers) ) {
			foreach ($controllers as $controller) {
				if( $this->_hasRequestIn(self::CONTROLLER, $controller, true) ) {
					return true;
				}
			}
		} else {
			return $this->_hasRequestIn(self::CONTROLLER, $controllers, true);
		}
	}

	/**
	 * @desc Returns whether the action with the above syntax given as parameter is ignored in this container.
	 * If the action is ignored, it returns true.
	 * 
	 * Syntax: moduleName/controllerName/actionName
	 * 
	 * @param string|array $actions With the specific syntax
	 * @return bool true if the action with the specific syntax is ignored
	 */
	public function isActionIgnored($actions)
	{
		if( is_array($actions) ) {
			foreach ($actions as $action) {
				if( $this->_hasRequestIn(self::ACTION, $action, true) ) {
					return true;
				}
			}
		} else {
			return $this->_hasRequestIn(self::ACTION, $actions, true);
		}
	}
	
	private function _hasRequestIn($type, $request, $ignoredArray = false)
	{
		$chunks = $this->_splitRequest($type, $request);
		
		$arrays = array();
		switch( $type ) {
			case self::ACTION:
				$arrays = $ignoredArray ?  $this->_ignoredActions : $this->_actions;
				break;
			case self::CONTROLLER:
				$arrays = $ignoredArray ? $this->_ignoredControllers:  $this->_controllers;
				break;
		}
		
		foreach ($arrays as $array) {
			if( count(array_diff($array, $chunks)) == 0 ) {
				return true; 
			}
		}
		
		return false;
	}
	
	private function _addRequestTo(&$into, $type, $request)
	{
		$chunks = $this->_splitRequest($type, $request);
			
		$array = array($chunks[0], $chunks[1]);
		
		if( count($chunks) > 2 ) {
			$array[] = $chunks[2];
		}
		
		array_push($into, $array);
	}
	
	private function _splitRequest($type, $request)
	{
		$chunks = split('/', $request);
		
		$this->_validateRequest($type, $request, $chunks);
		
		return $chunks;
	}
	
	private function _validateRequest($type, $request, &$chunks)
	{
		$count = count($chunks);
		
		$syntaxError = 'Wrong syntax, the syntax must be: ';
		
		switch( $type ) {
			case self::ACTION:
				if( $count != 3 ) {
					throw new Majisti_Callback_Exception($syntaxError . 'moduleName/controllerName/actionName');
				}
				break;
			case self::CONTROLLER:
				if( $count != 2 ) {
					throw new Majisti_Callback_Exception($syntaxError . 'moduleName/controllerName');
				}
				break;
			case self::MODULE:
				if( $count != 1 ) {
					throw new Majisti_Callback_Exception($syntaxError . 'actionName');
				}
				break;
		}
	}
}