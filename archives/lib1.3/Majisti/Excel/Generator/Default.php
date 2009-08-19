<?php

/**
 * @desc Generates an excel file. Headers will be written on the first row and will have a special format
 * (bold underlined centered in its cell). Data will then be dumped and centered in its cell under each
 * header. Columns automatically fits to either the data or header string length so that the data or header is always
 * visible without having to resize the column while in the excel editor. The auto-resizing will resize to a maximum
 * width according to the $_maximumResizeWidth.
 * 
 * FIXME: auto-resize still has some minor glitches
 * TODO: implement maximumResizeWidth
 * 
 * @author Steven Rosato
 */
class Majisti_Excel_Generator_Default extends Majisti_Excel_Generator_Abstract
{
	private $_headersWidth = array();
	
	private $_headersWritten = false;
	
//	private $_maximumResizeWidth;

//	/**
//	 * @desc Constructs a generator with the $filename provided. The adapter given will serve as the
//	 * data source that will be dumped in the excel file.
//	 *
//	 * @param Majisti_Excel_Writer_Adapter_Abstract $adapter The data source adapter
//	 * @param String $filename The complete absolute path and file name the excel should be saved as.
//	 */
//	public function __construct(Majisti_Excel_Writer_Adapter_Abstract $adapter, $filename, $maximumResizeWidth = 50)
//	{
//		$this->_writer = new Majisti_Excel_Writer($filename);
//		$this->_adapter = $adapter;
//	}

	/**
	 * Unicode is not supported by PEAR Spreadsheet, therefore,
	 * a quick fix to decode utf8 is added here
	 */
	private function _decode($str)
	{
		if( mb_detect_encoding($str) === 'UTF-8' ) {
			$str = utf8_decode($str);
		}
		
		return $str;
	}
	
	/**
	 * @desc Writes the headers to the worksheet. Every header will be bolded, underlined and
	 * aligned to the center of its cell.
	 *
	 * @param Majisti_Excel_Writer_Worksheet $worksheet The worksheet
	 * @param array $headers The headers
	 */
	protected function _writeHeaders(Spreadsheet_Excel_Writer_Worksheet $worksheet, array $headers)
	{
		/* format */
		$format = $this->_writer->getWorkbook()->addFormat();
		$format->setBold();
		$format->setUnderline(1);
		$format->setAlign('center');
		
		/* write headers */		
		for ($i = 0 ; $i < count($headers) ; $i++) {
			$this->_headersWidth[] = strlen($headers[$i]);
			$worksheet->write(0, $i, $this->_decode($headers[$i]), $format);
		}
		$this->_headersWritten = true;
	}
	
	/**
	 * @desc Writes the data and centerize it in its cell. The columns will auto-resize.
	 *
	 * @param Majisti_Excel_Writer_Worksheet $worksheet The worksheet to dump the data to
	 * @param array $data The data
	 */
	protected function _writeData(Spreadsheet_Excel_Writer_Worksheet $worksheet, array $data)
	{
		$format = $this->_writer->getWorkbook()->addFormat();
		$format->setAlign('center');
		
		/* dump data */
		for ($i = 0 ; $i < count($data) ; $i++) {
			$j = 0;
			$largestValueWidth = 0;
			foreach ($data[$i] as $value) {
				$value = stripslashes($value);
				if( strlen($value) > $largestValueWidth ) {
					$largestValueWidth = strlen($value);
				}
				if( $this->_headersWritten ) {
					$worksheet->write($i + 1, $j, $this->_decode($value), $format);
				} else {
					$worksheet->write($i, $j, $this->_decode($value), $format);
				}
				$j++;
			}
			/* resize according to what is bigger */
			if( count($this->_headersWidth) - 1 >= $i ) {
				if( $largestValueWidth > $this->_headersWidth[$i] ) {
					$worksheet->setColumn($i, $i+1, $largestValueWidth);
				} else {
					$worksheet->setColumn($i, $i+1, $this->_headersWidth[$i]);
				}
			}
		}
	}
}