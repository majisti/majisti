<?php

/**
 * TODO: doc
 * 
 * @author Steven Rosato
 */
class Majisti_View_Helper_IsUserLogged extends Zend_View_Helper_Abstract
{
	public function isUserLogged()
	{
		return Zend_Auth::getInstance()->hasIdentity();
	}
}