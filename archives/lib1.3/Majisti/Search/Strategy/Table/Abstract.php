<?php

abstract class Majisti_Search_Strategy_Table_Abstract implements Majisti_Search_Strategy_Interface
{
	/* @var $_table Zend_Db_Table_Abstract */
	protected $_table;
	protected $_column;
	
	public function __construct(Zend_Db_Table_Abstract $table, $column)
	{
		if( empty($column) ) {
			throw new Majisti_Strategy_Exception("Column can't be empty");
		}
		
		$this->_table 	= $table;
		$this->_column = $column;
	}
}