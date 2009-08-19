<?php

/**
 * TODO: doc
 *
 * @author Yanick Rochon
 */
class Majisti_View_Helper_Listing_List extends Majisti_View_Helper_Listing_Abstract {

	/**
	 * The default body script
	 */
	const DEFAULT_BODY = 'partials/listing/list/default.phtml';
	
	
	public function __construct($view, $options = array()) {
		
		parent::__construct($view, $options);
		
		$body = $this->getBody();
		if ( empty($body) ) {
			$this->setBody( self::DEFAULT_BODY );
		}
		
	}

	/**
	 * (non-PHPdoc)
	 * @see Majisti/View/Helper/Listing/Abstract#preRender()
	 */
	public function preRender(& $options)
	{
		
	}


}