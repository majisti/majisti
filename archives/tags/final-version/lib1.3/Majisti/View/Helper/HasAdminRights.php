<?php

/**
 * TODO: doc
 * 
 * @author Steven Rosato
 */
class Majisti_View_Helper_HasAdminRights extends Zend_View_Helper_Abstract 
{
	public function hasAdminRights()
	{
		if( $this->view->isUserLogged() ) {
			return Majisti_User::getInstance()->isAdmin();
		}
		return false;
	}
}