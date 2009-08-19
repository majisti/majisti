<?php

/**
 * A database adapter that can be used in conjunction with Majisti_Excel_Generator.
 * 
 * @author Steven Rosato
 */
class Majisti_Excel_Writer_Adapter_Db extends Majisti_Excel_Writer_Adapter_Abstract 
{
	private $_sqlSource;
	
	/**
	 * @desc Constructs a Db adapter that will fetch the data from a database. It is assumed that the database
	 * connexion was properly established with the factory before instanciating this class.
	 *
	 * @param mixed $table A table or name or an instance of Zend_Db_Select | Zend_Db_Table_Select.
	 * If a select object is passed, the select should have been previously prepared for fetching as it will
	 * be used 'as is' in the process of fetching in the getData() function. The headers will then need to be given as well.
	 * @param array $headers (optionnal, default = table's columns) The headers that will appear in the excel file. It is mendatory
	 * if a select object is given for $table but optionnal if $table is a table name. 
	 * The default headers will then result as being the table's columns names.
	 */
	public function __construct($table, array $headers = null)
	{
		/* instanciate a table */
		if( is_string($table) ) {
			
			$this->_sqlSource 	= new Majisti_Excel_Writer_Adapter_Db_Results($table);
			$this->_headers 	= $headers == null ? $this->_sqlSource->info('cols') : $headers;
			
		/* instance of a select object, headers must be provided */
		} else if( $table instanceof Zend_Db_Select || $table instanceof Zend_Db_Table_Select ) {
			if( $headers == null ) {
				throw new Majisti_Excel_Writer_Adapter_Exception("Headers can't be null when using a select object instance");
			}
			$this->_sqlSource 	= $table;
			$this->_headers 	= $headers;
		}
	}
	
	/**
	 * @desc Returns the data fetched from the table or the select object depending on
	 * what was given as parameter at class constructor
	 *
	 * @return Array The data fetched
	 */
	public function getData()
	{
		if( $this->_sqlSource instanceof Zend_Db_Select ) {
			return $this->_sqlSource->getDefaultAdapter()->fetchAll($this->_sqlSource);
		} else if( $this->_sqlSource instanceof Zend_Db_Table_Select ) {
			return $this->_sqlSource->getAdapter()->fetchAll($this->_sqlSource)->toArray();
		} else {
			return $this->_sqlSource->fetchAll($this->_sqlSource->select())->toArray();
		}
	}
}
