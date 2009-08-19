<?php

/**
 * TODO: doc
 * 
 * @deprecated
 * 
 * @author Steven Rosato
 */
class Majisti_View_Helper_ActionName extends Zend_View_Helper_Abstract 
{	
	public function actionName()
	{
		return Zend_Controller_Front::getInstance()->getRequest()->getActionName();
	}
}