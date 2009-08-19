<?php

class Majisti_Excel_Writer_Adapter_Db_Results extends Zend_Db_Table_Abstract
{
	protected $_name;
	
	public function __construct($tableName, $config = null)
	{
		$this->_name = $tableName;
		parent::__construct($config);
	}
}