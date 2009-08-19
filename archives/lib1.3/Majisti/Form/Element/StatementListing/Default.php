<?php

/**
 * @desc Represents a default StatementListing. A default statement listing has its
 * title at the top of the table with it's listed choices to the right of it and bellow
 * this header has all its queries to the left, with the elements iterated to the right.
 * 
 * @author Steven Rosato
 */
class Majisti_Form_Element_StatementListing_Default extends Majisti_Form_Element_StatementListing_Abstract
{
	protected function _decorateComposite(Zend_Form_Element $composite, $query)
	{
		/* Serves as a second HtmlTag that will wrap the whole query */
		$queryDecorator = new Majisti_Form_Decorator_SecondHtmlTag(array('tag' => 'tr'));
		
		/* Swap colors */
		if( count($this->_elements) % 2 == 0 ) {
			$queryDecorator->setOption('class', 'query query_odd');	
		} else {
			$queryDecorator->setOption('class', 'query query_even');
		}
			
		/* decorators manipulation */
		$composite->removeDecorator('Errors');
		$composite->addDecorators(array(
			$queryDecorator, //tr
			new Majisti_Form_Decorator_StatementListing_Default_Errors()
		));
		$composite->getDecorator('HtmlTag')->setOptions(array('tag' =>'td', 'class' => 'choice'));
		$labelDecorator = $composite->getDecorator('Label');
		$labelDecorator
			->setReqPrefix('<span class="required">&nbsp;*&nbsp;</span>')
			->setOptions(array(
				'tag' 	 => 'td', 
				'class'  => 'query',
				'escape' => false
		));
		
		/* misc manipulations */
		$composite
			->setLabel($query)
			->setSeparator('</td><td class="choice">')
			->setRequired(true)
			->setBelongsTo($this->getName())
			->setTranslator(Majisti_Form::getDefaultTranslator())
		;
	}
	
	protected function _decorateSubtitleComposite(Zend_Form_Element $subtitleComposite)
	{
		/* wrap the description inside <tr><td> */
		$tr = new Zend_Form_Decorator_HtmlTag(array('tag' 	=> 'tr'));
		$td = new Majisti_Form_Decorator_SecondHtmlTag(array(
			'tag' 		=> 'td',
			'class'		=> 'subtitle',
			'colspan' 	=> count($this->_choices) + 1
		));
		
		/* Swap colors */
		if( count($this->_elements) % 2 == 0 ) {
			$tr->setOption('class', 'query query_odd');	
		} else {
			$tr->setOption('class', 'query query_even');
		}
		
		/* Add the only decorators we need for this anonymous element */
		$subtitleComposite->addDecorator('Description', array('tag' => 'span'));
		$subtitleComposite->addDecorator($td);
		$subtitleComposite->addDecorator($tr);
	}
	
	/**
	 * @desc Renders this concrete StatementListing. Printing an header
	 * a body with multiple queries and choices with the elements to the right.
	 *
	 * @param Zend_View_Interface $view The view
	 * @return String The HTML
	 */
	public function render(Zend_View_Interface $view = null)
	{
		/* tr and td tags used to wrap content */
		$tr = new Zend_Form_Decorator_HtmlTag(array('tag' => 'tr'));
		$td = new Zend_Form_Decorator_HtmlTag(array('tag' => 'td'));
		
		/*
		 * Header
		 */
		
		/* Render the title */
		$td->setOption('class', 'query');
		$content = $td->render($this->getTitle());
		
		/* Render the choices */
		foreach ($this->_choices as $key => $choice) {
			$td->setOption('class', 'header_choice header_choice_' . ($key + 1));
			$content .= $td->render($choice);
		}
		
		/* wrap in tr */
		$tr->setOption('class', 'header');
		$content = $tr->render($content);
		
		/*
		 * Body
		 */
		
		/* Render each element with their labels that serve as the query */
		foreach ($this->_elements as $element) {
			$content .= $element->render($view);
		}
		
		/* render inside the table tag */
		$content = $this->getDecorator('HtmlTag')->render($content);
		
		/* render inside the default form element wrapper (which is usually dd) */
		$content = $this->getDecorator('SecondHtmlTag')->render($content);
		
		/* render the label */
		$content = $this->getDecorator('Label')->setElement($this)->render($content);
		
		//done!
		return $content;
	}
}