<?php

namespace Majisti\Model;

/**
 * @desc Majisti Form overriding the setLayout() function in order to
 * accept any Majisti ILayout object wich defines two functions forming
 * a Visitor [GoF] pattern.
 * @see \Zend_Form
 * @author Majisti
 */
class Form extends \Zend_Form
{
    /**
     * @desc Overriding \Zend_Form setLayout function to allow the use
     * of Majisti ILayout objects in the form layout setting process.
     * @param Form\Layout\ILayout $layout
     */
    public function setLayout(Form\Layout\ILayout $layout)
    {
        $layout->visitForm($this);

        foreach ($this->getSubForms() as $subForm) {
        	$layout->visitForm($subForm);
        }
    }
}
