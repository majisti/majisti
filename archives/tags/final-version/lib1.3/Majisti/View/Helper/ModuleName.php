<?php

/**
 * TODO: doc
 * 
 * @author Steven Rosato
 */
class Majisti_View_Helper_ModuleName extends Zend_View_Helper_Abstract 
{	
	public function moduleName()
	{
		return Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
	}
}