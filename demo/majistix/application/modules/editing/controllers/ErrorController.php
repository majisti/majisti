<?php

class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        $boot = $this->getInvokeArg('bootstrap');
        if ($boot->hasResource('logger')) {
            $boot->getResource('logger')->log($errors->exception, Zend_Log::ERR);
        }
    }
}
