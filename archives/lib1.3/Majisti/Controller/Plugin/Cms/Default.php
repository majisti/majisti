<?php

/**
 * Content management system plugin loader.
 *
 */
class Majisti_Controller_Plugin_Cms_Default extends Majisti_Controller_Plugin_Abstract
{

	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{

		$config = $this->getConfig();

		// TODO : implement this

		//   This function should initialize the CMS component. To actually
		//   output the necessary HTML / Javascripts, the controller or
		//   view scripts must use Majisti_Cms::getInstance()'s methods

		//Majisti_Cms::getInstance();

	}


}
