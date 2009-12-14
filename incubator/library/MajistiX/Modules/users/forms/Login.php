<?php

class Users_Form_Login extends Zend_Form
{
    public function init()
    {
        //TODO: use a translator and make label names flexible
        //TODO: add CSS classes for more flexibility
        
        /* login */
        $tf_login = new Zend_Form_Element_Text('login');
        $tf_login->setLabel('Login' . ':');
        
        $this->addElement($tf_login);
        
        /* password */
        $tf_pass = new Zend_Form_Element_Password('pass');
        $tf_pass->setLabel('Password' . ':');
        
        $this->addElement($tf_pass);
        
        /* submit button */
        $this->addElement(
            new Zend_Form_Element_Submit('submit', 'Submit'));
    }
}
