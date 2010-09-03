<?php

namespace Majisti\View\Helper;

class Request extends AbstractHelper
{
    public function request()
    {
        return \Zend_Controller_Front::getInstance()->getRequest();
    }
}
