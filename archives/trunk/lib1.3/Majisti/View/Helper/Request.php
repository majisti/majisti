<?php

/**
 * TODO: doc
 *
 * @author Steven Rosato
 */
class Majisti_View_Helper_Request extends Zend_View_Helper_Abstract
{
	public function request()
	{
		return Zend_Controller_Front::getInstance()->getRequest();
	}
}