<?php

/**
 * @desc Renders a Statement listing representing a concrete Composite and a Repetitive Element.
 * One must set the title, add choices, add queries and may change the default element rendered.
 * 
 * This will then generate a table based 'Statement Listing' with the passed options. Every 
 * composite elements will be required if the setRequired is used on this element.
 * 
 * @author Steven Rosato
 */
abstract class Majisti_Form_Element_StatementListing_Abstract extends Majisti_Form_Element_Repetitive
{
	const ELEMENT_TYPE_RADIO 			= 'Radio';
	const ELEMENT_TYPE_MULTICHECKBOX 	= 'Multicheckbox';
	
	/** @var string */
	private $_elementType = 'Radio'; //Radio or MultiCheckbox
	
	/**
     * @desc Constructor. Construct a concrete StatementListing
     *
     * $spec may be:
     * - string: name of element
     * - array: options with which to configure element
     * - Zend_Config: Zend_Config with options for configuring element
     * 
     * @param  string|array|Zend_Config $spec 
     * @return void
     * @throws Zend_Form_Exception if no element name after initialization
     */
	public function __construct($spec, $options = null)
	{
		parent::__construct($spec, $options);
		
		$tableClass = isset($options['table_class']) ? $options['table_class'] : 'statement_listing';
		unset($options['table_class']);
		
		/* the table and dd tags */
		$this->getDecorator('HtmlTag')->setOptions(array('tag' => 'table', 'class' => $tableClass));
		$this->addDecorator(new Majisti_Form_Decorator_SecondHtmlTag(array('tag' => 'dd')));
	}
	
	/**
	 * @desc Sets the default element type. Constants are available.
	 *
	 * @param String $elementType The element type
	 * @return This
	 */
	public function setDefaultElementType($elementType)
	{
		$this->_elementType = $elementType;
		return $this;
	}
	
	/**
	 * {@link Majisti_Form_Element_Repetitive::addQuery}
	 */
	public function addQuery($name, $query, $isSubtitle = false) 
	{
		if( count($this->_choices) <= 0 ) {
			throw new Majisti_Form_Element_Exception(
				'Choices for statement listing were never set,
				make sure that choices were added before adding queries.');
		}
		
		if( !$isSubtitle ) {
			/* instanciate the element that will be used in the statement */
			$elementType = 'Zend_Form_Element_' . $this->_elementType;
			$composite = new $elementType($name);

			$this->_decorateComposite($composite, $query);
			
			/* add empty options so we will have empty labels */
			reset($this->_choices);
			while( current($this->_choices) ) {
				$composite->addMultiOption(key($this->_choices), '');
				next($this->_choices);
			}

			$this->addElement($composite);
		} else { /* Subtitle */
			/* create an anonnymous element and make it appear as a tr with a td and description within */
			$subtitleComposite = new Zend_Form_Element($name . '_subtitle');
			$subtitleComposite->clearDecorators();
			$subtitleComposite->setDescription($query);
			
			$this->_decorateSubtitleComposite($subtitleComposite);
			
			$this->addElement($subtitleComposite);
		}
		return $this;
	}
	
	protected abstract function _decorateComposite(Zend_Form_Element $composite, $query);
	
	protected abstract function _decorateSubtitleComposite(Zend_Form_Element $subtitleComposite);
}