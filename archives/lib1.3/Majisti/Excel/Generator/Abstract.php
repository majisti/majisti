<?php

/**
 * @desc The Abstract Generator is used for every kind of concrete generator that will
 * implement different ways to dump the headers and data of an adapter. Currently with this adapter,
 * the data dumped is given only to one spreadsheet but this can be changed with concrete
 * classes or with a later implementation of decorators. Conditionnal formatting for cells should be implemented
 * in the future as well.
 *
 * @author Steven Rosato
 */
abstract class Majisti_Excel_Generator_Abstract
{
	protected $_writer;
	
	private $_adapter;
	
	/**
	 * @desc Constructs a generator with the $filename provided. The adapter given will serve as the
	 * data source that will be dumped in the excel file.
	 *
	 * @param Majisti_Excel_Writer_Adapter_Abstract $adapter The data source adapter
	 * @param String $filename The complete absolute path and file name the excel should be saved as.
	 */
	public function __construct(Majisti_Excel_Writer_Adapter_Abstract $adapter, $filename)
	{
		$this->_writer = new Majisti_Excel_Writer($filename);
		$this->_adapter = $adapter;
	}
	
	/**
	 * @desc Generates an excel file and dump the headers and data from the adapter
	 * to one spreadsheet.
	 */
	public function generate()
	{
		$workbook = $this->_writer->getWorkbook();
		$worksheet = $workbook->addWorksheet();
		
		$headers = $this->_adapter->getHeaders();
		if( count($headers) ) {
			$this->_writeHeaders($worksheet, $headers);
		}
		
		$this->_writeData($worksheet, $this->_adapter->getData());
		
		$workbook->close();
	}
	
	/**
	 * @desc Writes the headers to this worksheet.
	 *
	 * @param Majisti_Excel_Writer_Worksheet $worksheet The worksheet that the headers will be written on
	 * @param Array $headers The headers
	 */
	protected abstract function _writeHeaders(Spreadsheet_Excel_Writer_Worksheet $worksheet, array $headers);
	
	/**
	 * @desc Writes the data to this worksheet.
	 *
	 * @param Majisti_Excel_Writer_Worksheet $worksheet The worksheet that the data will be written on
	 * @param array $data The data
	 */
	protected abstract function _writeData(Spreadsheet_Excel_Writer_Worksheet $worksheet, array $data);
}