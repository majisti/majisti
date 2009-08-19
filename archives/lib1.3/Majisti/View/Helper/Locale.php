<?php

/**
 * @desc Returns the current locale previously setup in Majisti_I18n.
 * 
 * @author Steven Rosato
 */
class Majisti_View_Helper_Locale extends Zend_View_Helper_Abstract 
{	
	/**
	 * @desc Returns the current session language.
	 *
	 * @return String The current assigned language
	 */
	public function locale()
	{
		return Zend_Registry::get('Majisti_I18n')->getCurrentLocale();
	}
}