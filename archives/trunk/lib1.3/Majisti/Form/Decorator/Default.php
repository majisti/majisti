<?php

/**
 * Default form decorator to output a div based nice formatted (standard) forms using
 * 
 * Changelog
 * 	1.1 (Steven): 
 * 		- fixed W3C errors
 * 		- fixed that if an element had no label, the class won't output a label (previously it outputed one)
 * 
 * @author Yanick Rochon
 * @version 1.1
 */
class Majisti_Form_Decorator_Default extends Zend_Form_Decorator_Abstract
{
 	public function buildLabel()
	{
		$element 	= $this->getElement();
		$label 		= $element->getLabel();

		if( $label != null ) {
			if ($translator = $element->getTranslator()) {
			    $label = $translator->translate($label);
			}
			$label = $element->getView()->formLabel($element->getName(), $label);
		}
		
		return $label;
	}
	
	public function buildInput()
	{
		$element = $this->getElement();
		$helper  = $element->helper;
		$attribs = $element->getAttribs();
		
		unset($attribs['helper']);
		
		$input = $element->getView()->$helper(
			$element->getName(),
			$element->getValue(),
			$attribs,
			$element->options
		);
		
		if ($element->isRequired()) {
			$helpMsg = 'Mendatory Field';
			if ($translator = $element->getTranslator()) {
				$helpMsg = $translator->translate('mendatory_tooltip');
			}
			$input .= '<span class="required" title="' . $helpMsg . '">*</span>';
		}
		
		return $input;
	}
	
	public function buildErrors()
	{
		$element  = $this->getElement();
		$messages = $element->getMessages();
		if (empty($messages)) {
			return '';
		}
		return $element->getView()->formErrors($messages);
	}
	
	public function buildDescription()
	{
		$element = $this->getElement();
		$desc    = $element->getDescription();
		if (empty($desc)) {
			return '';
		}
		if ($translator = $element->getTranslator()) {
			$desc = $translator->translate($desc);
		}
		return $desc;
	}
	
	public function render($content)
	{
		$element = $this->getElement();
		
		if (!$element instanceof Zend_Form_Element) {
			return $content;
		}
		if (null === $element->getView()) {
			return $content;
		}
	
		$separator = $this->getSeparator();
		$placement = $this->getPlacement();
		$label     = $this->buildLabel();
		$input     = $this->buildInput();
		$errors    = $this->buildErrors();
		$desc      = $this->buildDescription();
		
		$output = '<div class="formElement">'
						  . '<div class="formLabel">' . $label . '</div>'
						  . '<div class="formInput">' . $input . '</div>'
						  . ( !empty( $errors )
						    ? '<div class="formErrors">' . $errors . '</div>'
						    : '' )
						  . ( !empty( $desc )
						  	? '<div class="formDescription">' . $desc . '</div>'
						  	: '' )
						. '</div>';
		
		if( $placement == self::PREPEND ) {
			return $output . $separator . $content;
		}
		
		return $content . $separator . $output;
	}
}