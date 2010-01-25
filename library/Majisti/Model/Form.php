<?php

namespace Majisti\Model;

class Form extends \Zend_Form
{
    public function setLayout(Form\Layout\ILayout $layout)
    {
        $layout->visitForm($this);

        foreach ($this->getSubForms() as $subForm) {
        	$layout->visitForm($subForm);
        }
    }
}
