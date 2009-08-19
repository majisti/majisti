<?php

/**
 * TODO: doc
 *
 * @author Steven Rosato
 */
abstract class Majisti_Validate_I18n_Abstract
{
	protected $_zendMessagesTemplates 		= array();

	protected $_majistiMessagesTemplates 	= array();
	
	public function getMessagesTemplates()
	{
		return array_merge($this->_zendMessagesTemplates, $this->_majistiMessagesTemplates);
	}
}