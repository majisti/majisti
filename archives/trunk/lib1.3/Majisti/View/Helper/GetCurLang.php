<?php

/**
 * @desc  Alias to Locale View Helper
 * @deprecated Use the alias instead.
 * 
 * @author Steven Rosato
 */
class Majisti_View_Helper_GetCurLang extends Zend_View_Helper_Abstract 
{	
	/**
	 * @desc Returns the current session language. Alias to Locale View Helper
	 *
	 * @return String The current assigned language
	 */
	public function getCurLang()
	{
		return $this->view->locale();
	}
}
