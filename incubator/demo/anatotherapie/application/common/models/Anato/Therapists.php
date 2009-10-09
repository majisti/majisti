<?php

/**
 * @desc Therapists list model.
 *
 * @author Steven Rosato
 */
class Anato_Therapists extends Majisti_Db_Table_General
{
	private $_therapistsRegions;
	
	/**
	 * @desc Constructs the therapists model
	 */
	public function __construct()
	{
		$this->_table = new Anato_Table_Therapists();
	}
	
	/**
	 * @desc Searches for therapists within the table based
	 * on a keyword and a column.
	 *
	 * @param String $keyword The keyword that the column will begin with
	 * @param String $column The column's name
	 * @param int $limit [opt; def=10] The maximum number of occurence to fetch
	 * 
	 * @return Zend_Db_Table_Rowset_Abstract The rowset
	 */
	public function search($keyword, $column, $limit = 10)
	{
		$select = $this->_table->select()
			->where($this->_table
				->getAdapter()
				->quoteIdentifier($column)
				. ' LIKE ?', $keyword . '%')
			->limit($limit);
		
		return $this->_table->fetchAll($select);					
	}
	
	/**
	 * @return int Returns the last inserted id
	 */
	public function getLastInsertId()
	{
		return (int)$this->_table->getAdapter()->lastInsertId();
	}
}
