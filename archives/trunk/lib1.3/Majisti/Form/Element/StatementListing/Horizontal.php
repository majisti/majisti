<?php

/**
 * @desc Reprensents an horizontal StatementListing. One big table is used to iterate
 * over all the queries, printing along the queries each in a table row and the listing
 * of the choices in another table row, aligned horizontaly. The title is prepended
 * as a table row as well.
 * 
 * @deprecated The functions setSuffix() get getSuffix() are replaced by setPostfix() and getPostfix()
 * but the CSS classes werent replaced yet, scheduled for 1.4
 * 
 * TODO: finish this class (put css classes and decorate element)
 * TODO: finish doc
 * 
 * @author Steven Rosato
 */
class Majisti_Form_Element_StatementListing_Horizontal extends Majisti_Form_Element_StatementListing_Abstract
{
	protected $_prefix;
	
	protected $_postfix;
	
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
		$options['table_class'] = 'horizontal_listing';
		parent::__construct($spec, $options);
	}
	
	public function getPrefix()
	{
		return $this->_prefix;
	}
	
	public function setPrefix($prefix)
	{
		$this->_prefix = $prefix;
	}
	
	/**
	 * @deprecated now following same convention as Zend_View_Helper_Placeholder
	 */
	public function getSuffix()
	{
		return $this->getPostfix();
	}
	
	/**
	 * @deprecated now following same convention as Zend_View_Helper_Placeholder
	 */
	public function setSuffix($suffix)
	{
		$this->setPostfix($suffix);
	}
	
	public function getPostfix()
	{
		return $this->_postfix;
	}
	
	public function setPostfix($postfix)
	{
		$this->_postfix = $postfix;
	}
	
	public function hasPrefix()
	{
		return $this->getPrefix() != null;	
	}
	
	public function hasPostfix()
	{
		return $this->getPostfix() != null;
	}
	
	protected function _decorateComposite(Zend_Form_Element $composite, $query)
	{
		/* sets the default decorators */
		$composite->setDecorators(array(
			array('Label', array('tag' => 'span')),
			'Errors',
			'ViewHelper',
		));
		
		/* misc manipulations */
		$composite
			->setLabel($query)
			->setSeparator('</td><td class="choice">')
			->setBelongsTo($this->getName())
			->setTranslator(Majisti_Form::getDefaultTranslator())
		;
	}
	
	protected function _decorateSubtitleComposite(Zend_Form_Element $subtitleComposite)
	{
		//TODO:
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
		
		/* Get the title in a <tr><td> */
		$tr->setOption('class', 'header');
		
		$colspan = count($this->_choices);
			
		if( $this->hasPrefix() ) {
			$colspan++;	
		}
		
		if( $this->hasPostFix() ) {
			$colspan++;
		}
		
		$td->setOption('colspan', $colspan);
		$td->setOption('class', 'header');
		
		$content = $tr->render($td->render($this->getTitle()));
		
		$tr->removeOption('class');
		$td->removeOption('colspan');
		$td->removeOption('class');
		
		/*
		 * Body
		 */
		
		/*
		 * Render each elements, and apply a different wrapping depending of the current
		 * element's decorator.
		 */
		foreach ($this->_elements as $element) {
			$tempContent = '';
			foreach ($element->getDecorators() as $decorator) {
				$decorator->setElement($element);
				
				/* Wrap label in a <tr><td> and give a colspan */
				if( $decorator instanceof Zend_Form_Decorator_Label ) {
					$tr->setOption('class', 'query');
					$td->setOptions(array('colspan' => count($this->_choices), 'class' => 'query'));
					$tempContent = $tr->render($td->render($decorator->render($tempContent)));
					$tr->removeOption('class');
					$td->removeOption('colspan');
					$td->removeOption('class');
				/* wrap view helper in a <tr><td> */
				} elseif ( $decorator instanceof Zend_Form_Decorator_ViewHelper ) {
					$temp = '';
					
					/* add the prefix if not null */
					if( $this->hasPrefix() ) {
						$td->setOption('class', 'prefix');
						$temp .= $td->render($this->getPrefix());
					}
					
					/* render the choices */
					$td->setOption('class', 'choice');
					$temp .= $td->render($decorator->render(''));
					
					/* add the suffix if not null */
					if( $this->hasPostfix() ) {
						$td->setOption('class', 'suffix');
						$temp .= $td->render($this->getSuffix());
					}
					
					/* wrap in tr */
					$tr->setOption('class', 'choices');
					$tempContent .= $tr->render($temp);
					
					/* remove the options */
					$tr->removeOption('class');
					$td->removeOption('class');
				/* append errors in a <tr><td> */
				} elseif ( $decorator instanceof Zend_Form_Decorator_Errors ) {
					$td->setOption('colspan', count($this->_choices));
					$tempContent .= $tr->render($td->render($decorator->render('')));
					$td->removeOption('colspan');
				/* render with only the decorator's default rendering */
				} else {
					$tempContent = $decorator->render($tempContent);
				}
			}
			$content .= $tempContent;
			
			/* apply another tr with the choices just bellow the elements, wrapped in td tags */
			$choicesContent = ''; 
			
			/* render empty td for prefix, if any */
			if( $this->hasPrefix() ) {
				$choicesContent .= $td->render('');
			}
			
			foreach ($this->_choices as $choice) {
				$td->setOption('class', 'choiceLabel');
				$choicesContent .= $td->render($choice);
				$td->removeOption('class');
			}
			
			/* render empty td for suffix, if any */
			if( $this->hasPostFix() ) {
				$choicesContent .= $td->render('');
			}
			
			$tr->setOption('class', 'choicesLabels');
			$content .= $tr->render($choicesContent);
			$tr->removeOption('class');
		}
		
		/* render inside the table tag */
		$content = $this->getDecorator('HtmlTag')->render($content);
		
		/* render inside the default form element wrapper (which is usually dd) */
		$content = $this->getDecorator('SecondHtmlTag')->render($content);
		
		/* render the label, if any */
		if( strlen($this->getLabel()) > 0 ) {
			$content = $this->getDecorator('Label')->setElement($this)->render($content);
		}
		
		//done!
		return $content;
	}
}