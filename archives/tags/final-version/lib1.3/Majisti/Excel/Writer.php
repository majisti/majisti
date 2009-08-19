<?php

class Majisti_Excel_Writer
{
	/** @var Spreadsheet_Excel_Writer */
	private $_workbook;
	
	const EXTENSION = 'xls';
	
	public function __construct($filename)
	{
		$libraries = dirname(__FILE__);
		
		while( !empty($libraries) && basename($libraries) != 'Majisti' ) {
			$libraries = dirname($libraries);
		}
		
		$libraries = dirname($libraries) . '/Libraries/';
		
		set_include_path(
			$libraries . 'PEAR/PEAR/' .PATH_SEPARATOR .
			$libraries . 'PEAR/' . PATH_SEPARATOR .
			$libraries . PATH_SEPARATOR . get_include_path()
		);
			
		$this->_workbook = new Majisti_Excel_Writer_Workbook($filename . '.' . self::EXTENSION);
	}
	
	public function getWorkbook()
	{
		return $this->_workbook;
	}
}