<?php

namespace Majisti\Model\Form\Layout;

/**
 * @desc Concrete table layout to be used with Majisti Form.
 * The table layout sets the elements and buttons to be in a HTML table setup.
 * Every element will be placed in a table row.
 */
class Table implements ILayout
{
    /**
     * @desc Setting the form elements decorator and individual element
     * decorator.
     * @param \Zend_Form $form
     */
    public function visitForm(\Zend_Form $form)
    {
        $elementDecorators = array(
                'ViewHelper',
                'Errors',
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td', 'class' => 'label')),
                array('Description', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'class' => 'row')),
        );

        $buttonDecorators = array(
            'ViewHelper',
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element button')),
            array(array('label' => 'HtmlTag'), array('tag' => 'td', 'placement' => 'prepend', 'class' => 'label')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'class' => 'row')),
        );

        $fileDecorators = array('File') + $elementDecorators;

        $formDecorators = array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form',
        );

        if( $form instanceof \Zend_Form_SubForm ) {
            array_pop($formDecorators);
            $formDecorators[] = 'Fieldset';
            $formDecorators[] = array(array('data' => 'HtmlTag'),
                array('tag' => 'td', 'class' => 'element', 'colspan' => 2));
            $formDecorators[] = array(array('row' => 'HtmlTag'),
                array('tag' => 'tr', 'class' => 'row'));
        }

        $form->setDecorators($formDecorators);

        $form->setElementDecorators($elementDecorators);

        /* @var $element \Zend_Form_Element */
        $captchaDecorators = array(
                'Errors',
                array(array('td' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element captcha')),
                array('Label', array('tag' => 'td', 'class' => 'label')),
                array('Description', array('tag' => 'td')),
                array(array('tr' => 'HtmlTag'), array('tag' => 'tr')),
        );

        foreach( $form->getElements() as $element ) {
            if( 'Zend_Form_Element_Captcha' === $element->getType() ) {
                $element->setDecorators($captchaDecorators);
            } elseif( 'Zend_Form_Element_File' === $element->getType() ) {
                $element->setDecorators($fileDecorators);
            } elseif( 'Zend_Form_Element_Submit' === $element->getType()
                || 'Zend_Form_Element_Reset' === $element->getType()
                || 'Zend_Form_Element_Hidden' === $element->getType() )
            {
                $element->setDecorators($buttonDecorators);
            }
        }

        foreach( $form->getDisplayGroups() as $dg ) {
            $dg->setDecorators(array(
//                array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'class' => 'row')),
                'FormElements',
                array(array('group' => 'HtmlTag'), array('tag' => 'table', 'class' => 'group')),
                'Fieldset',
                array(array('column' => 'HtmlTag'), array('tag' => 'td', 'class' => 'column', 'colspan' => 2)),
//                array(array('label' => 'HtmlTag'), array('tag' => 'td', 'placement' => 'prepend', 'class' => 'label')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr', 'class' => 'row')),
            ));
        }

    }

    /**
     * @desc Customizing a single form element.
     * @param \Zend_Form_Element $element
     */
    public function visitElement(\Zend_Form_Element $element)
    {

    }
}