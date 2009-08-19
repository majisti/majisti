<?php

class Majisti_Db_Table_General
{
	/**
	 * @var Zend_Db_Table_Abstract
	 */
	protected $_table;
	/**
	 * @var string
	 */
	protected $_primary;
	
	public function __construct($table = null, $primaryKey = 'id')
	{
		$this->_table = $table;
		$this->_primary = $primaryKey;
	}
	
	private function _appendFunctions($targetObject, array $functions = array()) {
		foreach ($functions as $function => $params) {
			if( is_array($params) ) {
				call_user_func_array(array($targetObject, $function), $params);
			} else {
				call_user_func(array($targetObject, $function), $params);
			}
		}
	}
	
	/**
	 * @return Zend_Db_Select All the users
	 */
	public function getAll($returnSelect = false, array $functions = array())
	{
		$select = $this->_table->select();
		
		$this->_appendFunctions($select, $functions);
//		print $select->assemble();exit;
		
		if( $returnSelect ) {
			return $select;
		}
		
		return $this->_table->fetchAll($select);
	}
	
	public function getById($id)
	{
		$rowset = $this->_table->find($id);

		if( $rowset->count() > 0 ) {
			return $rowset->current();
		}
		
		return null;
	}
	
	/**
	 * @desc Try to return the last inserted id from the database
	 *
	 * @return mixed
	 */
	public function getLastInsertedId() {
		// NOTE : MySQL ignores the parameter in lastInsertId, but we provide it anyway just in case...
		return $this->_table->getAdapter()->lastInsertId($this->_table);
	}
	
	public function insert($data)
	{
		return $this->_table->insert($data);
	}
	
	public function has($id)
	{
		return $this->getById($id) != NULL;
	}
	
	public function deleteById($id)
	{
		return $this->_table->delete(reset($this->_table->info('primary')) . " = '$id'");
	}
	
	public function update(array $data, $id)
	{
		return $this->_table->update($data, reset($this->_table->info('primary')) . " = '$id'"); 
	}
	
	public function query($select) {
		return $this->_table->fetchAll($select);	
	}
	
	public function quote($value, $type = null) {
		return $this->_table->getAdapter()->quote($value, $type);		
	}
	
	public function truncate() {
		$this->_table->getAdapter()->query('TRUNCATE ' . $this->_table->info('name'));
	}
}