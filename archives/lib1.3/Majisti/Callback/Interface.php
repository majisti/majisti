<?php

/**
 * TODO: doc
 * 
 * @author Steven Rosato
 */
interface Majisti_Callback_Interface
{
	public function call(Zend_Controller_Request_Abstract $request, $params = array());
}