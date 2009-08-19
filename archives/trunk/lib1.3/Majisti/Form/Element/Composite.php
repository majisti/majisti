<?php

/**
 * @desc This class represents a composite of elements. While being an element
 * itself, the composite will aggregate multiple elements as part of it and
 * therefore functions like isRequired(), isValid(), setRequired(), etc... will
 * behave depending of each of this composite's children. Therefore, the composite
 * itself cannot be required, or valid, only its children are.  By default, the
 * composite doesn't have a ViewHelper decorator and when it is rendered, the
 * HtmlTag decorator is removed for W3C compliance. It is then possible to add as
 * much as standard elements as needed (other composites can be added as well) and
 * they will all render in the order that they were appended.  This composite is
 * also Countable and Iterable
 * @author Steven Rosato
 * @version 1.0
 * @updated 08-May-2009 9:24:31 PM
 */
class Majisti_Form_Element_Composite extends Zend_Form_Element
{
	/** @var Array */
	protected $_elements = array();
	
	/**
	 * @desc Constructs the composite. Removing the ViewHelper decorator at the same time
	 * 
	 * {@link Zend_Form_Element::__construct()}
	 */
	public function __construct($spec, $options = null)
	{
		parent::__construct($spec, $options);
		
		$this->removeDecorator('ViewHelper');
	}
	
	/**
	 * @desc When composite is cloned, all of is contained elements are cloned as well
	 */
	public function __clone()
	{
		foreach ($this->_elements as $element) {
			$this->_elements[$element->getName()] = clone $element;
		}
	}
	
	/**
	 * @desc Needed for the Countable interface
	 * 
	 * @return The composite's element count, the composite itself is not included in the count
	 */
	public function count()
	{
		return count($this->getElements());	
	}
	
	/**
	 * @desc Needed for the Iterator interface
	 */
	public function current()
	{
		return current($this->_elements);		
	}
	
	/**
	 * @desc Needed for the Iterator interface
	 */
	public function rewind()
	{
		reset($this->_elements);	
	}
	
	/**
	 * @desc Needed for the Iterator interface
	 */
	public function key()
	{
		return key($this->_elements);	
	}
	
	/**
	 * @desc Needed for the Iterator interface
	 * 
	 * @return bool
	 */
	public function valid()
	{
		return current($this->_elements) !== false;	
	}
	
	/**
	 * @desc Needed for the Iterator interface
	 */
	public function prev()
	{
		prev($this->_elements);	
	}
	
	/**
	 * @desc Needed for the Iterator interface
	 */
	public function next()
	{
		next($this->_elements);	
	}
	
	/**
	 * @desc Adds an element to this composite.
	 *
	 * @param Zend_Form_Element $element
	 * 
	 * @return Majisti_Form_Element_Composite
	 */
	public function addElement(Zend_Form_Element $element)
	{
		$this->_elements[$element->getName()] = $element;
		
		return $this;
	}
	
	/**
	 * @desc Adds multiple elements to this composite
	 * 
	 * @param Array $elements
	 * 
	 * @return Majisti_Form_Element_Composite
	 */
	public function addElements(array $elements)
	{
		foreach ($elements as $element) {
			$this->addElement($element);
		}
		
		return $this;
	}
	
	/**
	 * Returns the element by name.
	 *
	 * @param String $name The name of the element
	 * @return Zend_Form_Element | null
	 */
	public function getElement($name)
	{
		if( isset($this->_elements[$name]) ) {
			return $this->_elements[$name];
		}
		
		return null;
	}
	
	/**
	 * @desc Returns all the elements
	 *
	 * @return Array of Zend_Form_Element
	 */
	public function getElements()
	{
		return $this->_elements;
	}
	
	/**
	 * @desc Removes an element in the composite
	 * 
	 * @param String $name The name of the element to remove
	 * 
	 * @return Boolean True if the element existed and was removed, false otherwise.
	 */
	public function removeElement($name)
	{
		if( array_key_exists($name, $this->_elements) ) {
			unset($this->_elements[$name]);
			return true;
		}
		
		return false;
	}
	
	/**
	 * @desc Removes all the elements in the composite
	 * 
	 * @return Majisti_Form_Element_Composite
	 */
	public function clearElements()
	{
		foreach (array_keys($this->_elements) as $name) {
			$this->removeElement($name);
		}
		
		return $this;
	}
	
	/**
	 * @desc Valids through all of the composite's aggregated elements.
	 *
	 * @param mixed $value
	 * @param mixed $context
	 * 
	 * @return bool If the composite's aggregated elements are valid
	 * @see Zend_Form_Element::isValid() for more precise documentation
	 */
	public function isValid($value, $context = null)
	{
		$valid = true;
		
		/* valid that the composite's elements are valid */
		foreach ($this->_elements as $key => $element) {
			if (!isset($value[$key])) {
				$valid = $element->isValid(null, $value) && $valid;
			} else {
				$valid = $element->isValid($value[$key], $value) && $valid;
			}
		}
		
		return $valid;
	}
	
	/**
	 * @desc Sets the value(s) for this composite. Pass an associative array if you want
	 * the children to be populated. Otherwise, only the composite will have it's value setup.
	 * 
	 * array(
	 * 		elementName 		=> value,
	 *  	secondElementName 	=> value,
	 *  )
	 *  
	 * @param Array|String $value An array or a single string representing values to set
	 * 
	 * @return Majisti_Form_Element_Composite
	 */
	public function setValue($value)
	{
		if( is_array($value) ) {
			foreach ($this->getElements() as $element) {
				$elementName = $element->getName();
				if( array_key_exists($elementName, $value) ) {
					$element->setValue($value[$elementName]);	
				}
			}
		} else {
			parent::setValue($value);
		}
		
		return $this;
	}
	
	/**
	 * @desc Sets all of the composite's element requirement to true
	 * 
	 * @param bool $flag[optional;def=false] Required or not
	 * 
	 * @return Majisti_Form_Element_Composite
	 */
	public function setRequired($flag = true)
	{
		foreach ($this->getElements() as $element) {
			$element->setRequired($flag);	
		}
		
		return $this;
	}
	
	/**
	 * @desc Returns true if all the elements are required.
	 * If at least one element is not required, then this function returns false.
	 * 
	 * TODO: review this, should it return required if at least one element is required?
	 * Should we provide two functions for this or more explicit functions?
	 * maybe isRequiredAtLeastOneChild() and etc...? to discuss!
	 * 
	 * @return True if all elements are required
	 */
	public function isRequired()
	{
		$isRequired = false;
		
		foreach ($this->getElements() as $element) {
			if( $isRequired && !$element->isRequired() ) {
				$isRequired = false;
			} elseif( $element->isRequired() ) {
				$isRequired = true;
			}
		}
		
		return $isRequired;
	}
	
	/**
	 * @desc Renders the composite in a simple manner. For more complicated
	 * output, one should consider extending this composite and redefine
	 * the rendering.
	 *
	 * @param Zend_View_Interface $view
	 * 
	 * @return String The rendered HTML
	 */
	public function render(Zend_View_Interface $view = null)
	{
		/* Get content of of the composite's elements */
		$content = '';
		foreach ($this->_elements as $element) {
			$content .= $element->render($view);
		}
		
		/* Remove HtmlTag decorator, this will be valid W3C */
		$this->removeDecorator('HtmlTag');
		
		/* Apply the composite's decorators to the content, since the composite is an element by itself */
		foreach ($this->getDecorators() as $decorator) {
			$decorator->setElement($this);
			
			/* dont render decorator if the label is an empty string */
			if( $decorator instanceof Zend_Form_Decorator_Label && strlen($this->getLabel()) == 0 ) {
				continue;	
			}
			$content = $decorator->render($content);
		}

		//done!
		return $content;
	}
}