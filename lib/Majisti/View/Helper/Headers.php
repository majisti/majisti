<?php

namespace Majisti\View\Helper;

class Headers extends AbstractHelper
{
    /**
     * @desc Returns the common headers for this application
     *
     * @return output
     */
    public function helper()
    {
        return $this;
    }

    public function prepare()
    {
        /* @var $view \Majisti\View\View */
        $view = $this->view;

        $maj = $this->getConfig()->majisti;

        $view->headLink()->offsetSetStylesheet('core', $maj->app->baseUrl .
            '/styles/core.css');

        $view->headMeta()->appendHttpEquiv(
            'Content-Type', 'text/html; charset=UTF-8');

        $view->headTitle($maj->app->namespace, 'SET');
    }

    public function toString()
    {
        $view = $this->view;
        $maj  = $this->getConfig()->majisti;

        /* headers */
        $headers = array();
        $headers[] = $view->headMeta()->toString();
        $headers[] = $view->headLink()->toString();
        $headers[] = $view->headStyle()->toString();
        $headers[] = trim($view->jQuery(), PHP_EOL);
        $headers[] = $view->headScript()->toString();
        $headers[] = $view->headTitle();

        /* append PHP_EOL on non empty strings */
        $output = '';
        foreach ($headers as $header) {
            $output .= $header;

        	if( !empty($header) ) {
        	    $output .= str_repeat(PHP_EOL, 2);
        	}
        }

        return $output;
    }

    public function __toString()
    {
        return $this->toString();
    }
}
