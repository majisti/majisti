<?php

/**
 * @desc Represents a null adapter which is simply an empty array. This is usefull
 * when the default language needs no translation but the application
 * requires that an adapter was setup (Zend_Translate registry key for exemple)
 * 
 * @author Steven Rosato
 */
class Majisti_Translate_Adapter_Null extends Zend_Translate_Adapter_Array
{
	/**
	 * Constructs a null translate adapter.
	 */
	public function __construct()
	{
		parent::__construct(array(), null, array('disableNotices' => true));
	}
}