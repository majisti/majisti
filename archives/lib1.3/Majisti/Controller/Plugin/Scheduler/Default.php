<?php

/**
 * Initialize the default scheduler plugin with the given config (Zend_Config)
 *
 */
class Majisti_Controller_Plugin_Scheduler_Default extends Majisti_Controller_Plugin_Abstract
{

	/**
	 * Called before an action is dispatched by Zend_Controller_Dispatcher.
	 *
	 * This callback allows for proxy or filter behavior.  By altering the
	 * request and resetting its dispatched flag (via
	 * {@link Zend_Controller_Request_Abstract::setDispatched() setDispatched(false)}),
	 * the current action may be skipped.
	 *
	 * @param  Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function preDispatch( Zend_Controller_Request_Abstract $request )
	{}


}