<?php

/**
 * @desc Concrete table row adding a table prefix for easier
 * magic methods call for dependent tables
 * 
 * @author Steven Rosato
 */
class Anato_Util_Table_Row extends Majisti_Db_Table_Row_Abstract
{
	protected $_prefix = 'Anato_Table_';
}