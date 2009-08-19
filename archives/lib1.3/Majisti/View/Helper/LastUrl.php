<?php

/**
 * TODO: review design and if accepted, implement class
 *
 * @author Steven Rosato
 */
class Majisti_View_Helper_LastUrl extends Zend_View_Helper_Abstract 
{	
	public function lastUrl()
	{
		throw new Majisti_View_Exception('Helper not implemented yet');
		//TODO: getenv('HTTP_REFERER')
		return '';
	}
}