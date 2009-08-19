<?php

class Majisti_Search_Strategy_Table_StartWith extends Majisti_Search_Strategy_Table_Abstract
{
	public function search($words)
	{
		return $this->_table->fetchAll('`' . $this->_column . "` LIKE '" . $words . "%'");
	}
}