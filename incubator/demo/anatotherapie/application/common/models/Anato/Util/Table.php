<?php

/**
 * @desc Constructs a db table. It makes use of a concrete row object.
 * 
 * @author Steven Rosato
 */
class Anato_Util_Table extends Zend_Db_Table_Abstract
{
	/**
	 * @desc Constructs the table
	 *
	 * @param Array $options The options
	 */
	public function __construct($options = array())
	{
		$defaultOptions = array(
			'name' 		=> $this->getTableName(),
			'rowClass' 	=> 'Anato_Util_Table_Row',
		);
		
		parent::__construct(array_merge($defaultOptions, $options));
	}
	
	/**
	 * @desc Parse the object class transforming it
	 * in a lowercasedUnderscored table name, without
	 * the _Table in the class name.
	 * 
	 * Ex: a Anato_Table_Foo_Bar
	 * will return anato_foo_bar
	 *
	 * @return String the new table name
	 */
	private function getTableName()
	{
		$class = str_replace('_Table', '', get_class($this));
		
		$matches = array();
		preg_match('/(.*_)(.*)/', $class, $matches);
		
		$prefix = $matches[1];
		
		$filter = new Zend_Filter_Word_CamelCaseToUnderscore();
		$suffix = $filter->filter($matches[2]);
		
		return strtolower($prefix . $suffix);
	}
}