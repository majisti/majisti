<?php

/**
 * TODO: implement the class
 * 
 * @author Steven Rosato
 */
class Majisti_Form_Element_StatementListing_Vertical extends Majisti_Form_Element_StatementListing_Abstract
{
	public function __construct($spec, $options = null)
	{
		$options['table_class'] = 'vertical_listing';
		parent::__construct($spec, $options);
	}
	
	protected function _decorateComposite(Zend_Form_Element $composite, $query)
	{
		//TODO:
	}
	
	protected function _decorateSubtitleComposite(Zend_Form_Element $subtitleComposite)
	{
		//TODO:
	}
	
	public function render(Zend_View_Interface $view = null)
	{
		//TODO:
	}
}