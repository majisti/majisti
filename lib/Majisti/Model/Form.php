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
	 * Constructs the form and load the translation according to the passed options if any or by
	 * automatically applying the proper options if nothing was setup.
	 *
	 * @param Array $options
	 */
	public function __construct($options = null)
	{
		$this->addPrefixPath(
            '\Majisti\Model\Form\Decorator\\',
            'Majisti/Model/Form/Decorator', 'decorator'
        );
		$this->addElementPrefixPath(
            '\Majisti\Model\Form\Decorator\\',
            'Majisti/Model/Form/Decorator',
            'decorator'
        );
		$this->addDisplayGroupPrefixPath(
            '\Majisti\Model\Form\Decorator\\',
            'Majisti/Model/Form/Decorator'
        );

		parent::__construct($options);
	}

    /**
     * @desc Overriding \Zend_Form setLayout function to allow the use
     * of Majisti ILayout objects in the form layout setting process.
     *
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
