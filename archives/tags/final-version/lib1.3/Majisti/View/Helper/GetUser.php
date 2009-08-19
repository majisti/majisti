<?php

/**
 * TODO: doc
 * 
 * @author Steven Rosato
 */
class Majisti_View_Helper_GetUser extends Zend_View_Helper_Abstract
{
	public function getUser()
	{
		return Zend_Auth::getInstance()->getIdentity();
	}
}