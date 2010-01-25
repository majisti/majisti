<?php

namespace Majisti\Model\Form\Layout;

class Table implements ILayout
{
    public function visitForm(\Zend_Form $form)
    {
        $elementDecorators = array(
                'ViewHelper',
                'Errors',
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
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
    }

    public function visitElement(\Zend_Form_Element $element)
    {

    }
}