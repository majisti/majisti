<?php

/**
 * Default Errors Decorator for each element inside a Default StatementListing.
 * 
 * @author Steven Rosato
 */
class Majisti_Form_Decorator_StatementListing_Default_Errors extends Zend_Form_Decorator_Errors
{
	/**
	 * Renders the errors just after the label and before the closing td
	 *
	 * @param String $content The content
	 */
	public function render($content)
	{
		/* TODO: revaluate the performance of this approach */
		preg_match('/.*\/label>/Ums', $content, $matches);
		$front = $matches[0];
		
		preg_match('/<\/td>.*/ms', $content, $matches);
		$end = $matches[0];
		
        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        $errors = $element->getMessages();
        if (empty($errors)) {
            return $content;
        }

        $errors = $view->formErrors($errors, $this->getOptions());
        
        return $front . $errors . $end;
	}
}