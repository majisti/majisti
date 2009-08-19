<?php

/**
 * @desc This class represents a composite of multiple aggregated sets of elements.
 * This is particulary usefull when a large amount of questions possess the same
 * sets of repetitive elements for instance a listing a 7 radio buttons or 7
 * checkboxes, etc... Those 7 elements (the number is not a fixed one) represent
 * the choices that were given up before rendering the element. The class then
 * takes of the title, the queries and the choices to append a concrete
 * representation of this new composite element. Mathematically, having 10 queries
 * each having 10 choices would generate 100 elements (radio buttons for example)
 * @author Steven Rosato
 * @version 1.0
 * @updated 08-May-2009 9:24:30 PM
 */
abstract class Majisti_Form_Element_Repetitive extends Majisti_Form_Element_Composite
{
	/** @var string */
	protected $_title = '';
	
	/** @var Array */
	protected $_choices = array();
	
	/**
	 * @desc Gets the statement listing's main title
	 *
	 * @return string The title
	 */
	public function getTitle()
	{
		return $this->_title;
	}
	
	/**
	 * @desc Sets the repetitive element's main title
	 *
	 * @param string $title The title
	 * @return Majisti_Form_Element_Repetitive
	 */
	public function setTitle($title)
	{
		$this->_title = $title;	
		return $this;
	}
	
	/**
	 * @desc Add a choice to this repetitive element
	 *
	 * @param String|null $choice Add a choice, giving null will store an empty string.
	 * @param String [optionnal;def=null] The index that the choice will have
	 * 
	 * @return Majisti_Form_Element_Repetitive
	 * 
	 * @throws Majisti_Form_Exception If choice is not a String or index is an empty String
	 */
	public function addChoice($choice, $index = null)
	{
		if( null == $choice ) {
			$choice = '';
		} else if( !is_string($choice) ) {
			throw new Majisti_Form_Exception('Choice must be a string or null');
		}
		
		if( null !== $index ) {
			if( '' === $index ) {
				throw new Majisti_Form_Exception("Index can't be an empty String");
			}
			$this->_choices[$index] = $choice;
		} else {
			$this->_choices[] = $choice;
		}
		
		return $this;
	}
	
	/**
	 * @desc Add multiple choices to this repetitive element.
	 *
	 * @param array $choices The choices
	 * @return Majisti_Form_Element_Repetitive
	 */
	public function addChoices(array $choices)
	{
		foreach ($choices as $choice) {
			$this->addChoice($choice);
		}
		return $this;
	}
	
	/**
	 * @desc Adds a query to the composite which resolve as adding the same sets
	 * of repetitive elements based on the number of choices.
	 *
	 * @param String $name The query's name
	 * @param String $query The query's value
	 * @param String $isSubtitle If this query should be treated as a subtitle
	 * 
	 * @return Majisti_Form_Element_Repetitive
	 * 
	 * @throws Majisti_Form_Element_Exception If no choices were ever added.
	 */
	abstract public function addQuery($name, $query, $isSubtitle = false);
	
	/**
	 * @desc Add multiple queries at once. It must be a key/value based array.
	 * If the query should be treated as a subtitle, wrap it in another array
	 * such as: array('query1' => 'Query 1', 'query2' => array('Subtitle'))
	 * or array(array('query1' => 'Subtitle'))
	 *
	 * @param array $queries The queries to add
	 * @return Majisti_Form_Element_Repetitive
	 */
	public function addQueries(array $queries)
	{
		foreach ($queries as $key => $query) {
			if( is_array($query) ) {
				$this->addQuery($key, reset($query), true);
			} else {
				$this->addQuery($key, $query);
			}
		}
		return $this;
	}
}