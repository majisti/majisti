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
                array('Label', array('tag' => 'td')),
                array('Description', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        );

        $buttonDecorators = array(
            'ViewHelper',
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array(array('label' => 'HtmlTag'), array('tag' => 'td', 'placement' => 'prepend')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        );

        $form->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form',
        ));

        $form->setElementDecorators($elementDecorators);

        /* @var $element \Zend_Form_Element */
        $captchaDecorators = array(
                'Errors',
                array('HtmlTag', 'options' => array('tag' => 'td', 'class' => 'element captcha')),
                array('Label', array('tag' => 'td')),
                array('Description', array('tag' => 'td')),
        );

        $submitDecorators = $elementDecorators;
        unset($submitDecorators[3]);

        foreach( $form->getElements() as $element ) {
            if( 'Zend_Form_Element_Captcha' === $element->getType() ) {
                $element->setDecorators($captchaDecorators);

//                \Zend_Debug::dump($element->render());
//                $element->render();
            } elseif( 'Zend_Form_Element_Submit' === $element->getType() ) {
//                $element->setDecorators($submitDecorators);
            }
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