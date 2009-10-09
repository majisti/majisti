<?php

/**
 * @desc Loads an XML file but parses all its data, transforming BbCode to HTML
 * 
 * @author Steven Rosato
 */
class Anato_Util_Config_Xml extends Zend_Config_Xml 
{
	private $_bbCodeParser;
	
	/**
	 * @desc Constructs the xml object
	 * 
	 * @see Zend_Config_Xml
	 */
	public function __construct($filename, $section = null, $allowModifications = false)
	{
		parent::__construct($filename, $section, true);
		
		$bbCode = Zend_Markup::factory('BbCode', 'Html');
		$bbCode->addTag('br', Zend_Markup::REPLACE_SINGLE, array('replace' => '<br />'));
		
		$this->_bbCodeParser = $bbCode;
		
		$this->_parseData($this);
		
		if( !$allowModifications ) {
			$this->setReadOnly();
		}
	}
	
	/**
	 * @desc Parses each data and convert BBCode to HTML
	 *
	 * @param Zend_Config $data The data to parse recursively
	 */
	private function _parseData($data)
	{
		foreach ($data as $key => $datum) {
			if( $datum instanceof Zend_Config ) {
				$this->_parseData($datum);
			} else {
				$data->$key = self::_escapeData($datum);
			}
		}
	}
	
	/**
	 * @desc Escape a loaded data by parsing bbcode and transforming it
	 * into HTML
	 *
	 * @param String $data
	 * @return String the HTMLized data
	 */
	private function _escapeData($data)
	{
		return !empty($data) 
			? $this->_bbCodeParser->render($data)
			: $data;
	}
}