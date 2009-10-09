<?php

/**
 * @desc This is the regions list model. Therapists
 * and Regions have a many to many relationship.
 *
 * @author Steven Rosato
 */
class Anato_Regions extends Majisti_Db_Table_General
{
	/**
	 * @desc Constructs the regions model
	 */
	public function __construct()
	{
		$this->_table = new Anato_Table_Regions();
	}
}
