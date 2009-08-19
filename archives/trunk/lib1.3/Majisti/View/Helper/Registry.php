<?php

/**
 * TODO: doc
 * 
 * @author Steven Rosato
 */
class Majisti_View_Helper_Registry extends Zend_View_Helper_Abstract
{
	public function registry($key)
	{
		return Zend_Registry::get($key);
	}
}