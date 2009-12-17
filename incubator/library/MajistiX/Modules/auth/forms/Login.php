<?php

/**
 * The login form model. It is composed of username and password fields
 * along with a submit button. It provides necessary CSS classes
 * for ajax addon and style customization.
 * 
 * @author Steven Rosato
 */
class Auth_Form_Login extends Zend_Form
{
    /**
     * @desc Inits the form
     */
    public function init()
    {
        //TODO: use a translator and make label names flexible
        //TODO: add CSS classes for more flexibility and ajax capabilities
        
        /* login */
        $tf_login = new Zend_Form_Element_Text('login');
        $tf_login
            ->setLabel('Login' . ':')
            ->setRequired()
        ;
        
        $this->addElement($tf_login);
        
        /* password */
        $tf_pass = new Zend_Form_Element_Password('pass');
        $tf_pass
            ->setLabel('Password' . ':')
            ->setRequired()
        ;
        
        $this->addElement($tf_pass);
        
        /* submit button */
        $this->addElement(
            new Zend_Form_Element_Submit('submit', 'Submit'));
    }
}
