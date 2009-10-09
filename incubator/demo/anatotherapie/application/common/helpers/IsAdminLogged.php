<?php

/**
 * @desc Helper method to determine whether an admin is already logged or not
 *
 * @author J-F Hamelin
 */

class Anatotherapie_View_Helper_IsAdminLogged extends Zend_View_Helper_Abstract
{	
	/**
	 * @return bool True is the admin is already logged, false otherwise
	 */
	public function isAdminLogged()
	{
		$session = new Zend_Session_Namespace(Zend_Registry::get('config')->session . "Admin");
		return (isset($session->allowed) && $session->allowed);
	}
}