<?php

namespace Majisti\Model\Form\Layout;

interface ILayout
{
    public function visitForm(\Zend_Form $form);
    public function visitElement(\Zend_Form_Element $element);
}
