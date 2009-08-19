<?php

/**
 * @desc Adds table prefix support for finding dependent tables
 * 
 * @author Steven Rosato
 */
abstract class Majisti_Db_Table_Row_Abstract extends Zend_Db_Table_Row_Abstract
{
	protected $_prefix = '';
	
	public function findManyToManyRowset($matchTable, $intersectionTable, $callerRefRule = null,
                                         $matchRefRule = null, Zend_Db_Table_Select $select = null)
	{
		return parent::findManyToManyRowset(
			$this->_prefix . $matchTable,
			$this->_prefix  . $intersectionTable,
			$callerRefRule,
			$matchRefRule,
			$select
		);
	}
	
	public function findDependentRowset($dependentTable, $ruleKey = null, Zend_Db_Table_Select $select = null)
	{
		return parent::findDependentRowset($this->_prefix  . $dependentTable, $ruleKey, $select);
	}
	
	public function findParentRow($parentTable, $ruleKey = null, Zend_Db_Table_Select $select = null)
	{
		return parent::findParentRow($this->_prefix  . $parentTable, $ruleKey, $select);
	}
}