<?php

/**
 * @desc The login form model. It is composed of username and password fields
 * along with a submit button. It provides necessary CSS classes
 * for AJAX capabilities and style customization.
 *
 * @author Steven Rosato
 */
class Auth_Form_Login extends Zend_Form
{
    protected   $_useAjax       = false;
    private     $_ajaxAttached  = false;

    public function setUseAjax($useAjax = true)
    {
        $this->_useAjax = (bool)$useAjax;
    }

    public function isAjaxUsed()
    {
        return $this->_useAjax;
    }

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

    public function render()
    {
        /* use ajax for form submition */
        if( $this->isAjaxUsed() && !$this->_ajaxAttached ) {
            $this->_attachAjax();
            $this->_ajaxAttached = true;
        }

        return parent::render();
    }

    protected function _attachAjax()
    {
        $view = $this->getView();

        $view->jQuery()->onLoadCaptureStart();?>
        callback = function() {
            $.post('<?=$view->url()?>',
                    {'login': $('#login').val(), 'pass' : $('#pass').val()}, function(data) {
                        $('#majisti_login-wrapper').html(data);
                        $('#majisti_login-wrapper form').submit(callback);
                    });
            return false;
        };

        $('#majisti_login-wrapper form').submit(callback);
        <?$view->jQuery()->onLoadCaptureEnd();
    }
}
