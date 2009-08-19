<?php

/**
 * TODO: doc
 *
 * @author Steven Rosato
 * 
 * @deprecated A multilingual subform will soon be available
 */
class Majisti_Form_SubForm_Multilingual extends Majisti_Form_Multilingual
{
	/**	
     * Whether or not form elements are members of an array
     * @var bool
     */
    protected $_isArray = true;

    /**
     * Load the default decorators
     * 
     * @return void
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormElements')
                 ->addDecorator('HtmlTag', array('tag' => 'dl'))
                 ->addDecorator('Fieldset')
                 ->addDecorator('DtDdWrapper');
        }
    }
}