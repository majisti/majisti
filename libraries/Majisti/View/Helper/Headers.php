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
        $view = $this->view;

        $maj = $this->getConfig()->majisti;

        $view->headLink()->appendStylesheet($maj->url . '/styles/majisti.css');
        $view->headLink()->appendStylesheet($maj->app->url .
            '/styles/main/main.css');

        $view->headMeta()->appendHttpEquiv(
            'Content-Type', 'text/html; charset=UTF-8');
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
        $headers[] = $view->headTitle($maj->app->namespace);

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
