<?php

//namespace Majisti\View\Helper;

/**
 * TODO: doc
 *
 * @author Majisti
 */
class Majisti_View_Helper_BaseUrl extends HelperAbstract
{
    public function baseUrl()
    {
        return \Zend_Controller_Front::getInstance()->getBaseUrl();
    }
}