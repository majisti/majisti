<?php

/**
 * TODO:
 * 
 * @author Steven Rosato
 */
class Majisti_View_Helper_GetAcl extends Zend_View_Helper_Abstract
{
	/**
	 * Returns the registered ACL from the registry
	 *
	 * @return Zend_Acl object. Null returned if no acl registered in the registry
	 */
	public function getAcl($regKey = 'acl')
	{
		if( Zend_Registry::isRegistered($regKey) ) {
			return Zend_Registry::get($regKey);
		}
		return null;
	}
}