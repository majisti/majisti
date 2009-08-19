<?php

/**
 * TODO: doc
 *
 * @author Yanick Rochon
 */
class Majisti_View_Helper_Listing_Table extends Majisti_View_Helper_Listing_Abstract {


	/**
	 * The default body script
	 */
	const DEFAULT_BODY = 'partials/listing/table/default.phtml';
	
	/**
	 * The default header visibility
	 */
	const DEFAULT_HEADER_VISIBLE = true;
	
	/**
	 * @var array<string>
	 */
	private $_columns;

	/**
	 * @var bool
	 */
	private $_tableHeaderVisible;

	
	public function __construct($view, $options = array()) {

		parent::__construct($view, $options);

		if ( isset($options['tableHeaderVisible']) ) {
			$this->_tableHeaderVisible = $options['tableHeaderVisible'];
		} else {
			$this->_tableHeaderVisible = self::DEFAULT_HEADER_VISIBLE;
		}
		
		$body = $this->getBody();
		if ( empty($body) ) {
			$this->setBody( self::DEFAULT_BODY );
		}
	}

	/**
	 *
	 * @return array<string>
	 */
	public function getColumns()
	{
		return $this->_columns;
	}

	/**
	 *
	 * @return bool
	 */
	public function isTableHeaderVisible()
	{
		return $this->_tableHeaderVisible;
	}

	/**
	 * (non-PHPdoc)
	 * @see Majisti/View/Helper/Listing/Abstract#preRender()
	 */
	public function preRender(& $options)
	{
		$tableOptions = new Majisti_Object();

		//$this->getView()->placeHolder('Majisti.Paginator.tableHeader')->set(TRUE);
		$tableOptions->headerVisible = $this->isTableHeaderVisible();

		//$this->getView()->placeHolder('Majisti.Paginator.tableColumns')->set($this->getColumns());
		$tableOptions->columns = $this->getColumns();
		
		// set the table options into the options array
		$options['table'] = $tableOptions;
	}

	/**
	 *
	 * @param $columns array
	 * @return Majisti_View_Helper_Listing_Table
	 */
	public function setColumns($columns)
	{
		$this->_columns = $columns;

		return $this;
	}

	/**
	 *
	 * @param (optional) boolean $state = TRUE
	 * @return Majisti_View_Helper_Listing_Options
	 */
	public function setTableHeaderVisible($state = TRUE)
	{
		$this->_tableHeaderVisible = $state;

		return $this;
	}


}