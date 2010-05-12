<?php

/**
 * @desc Returns common headers for this application
 *
 * @author Steven Rosato
 */
class MajistiD_View_Helper_Headers extends Zend_View_Helper_Abstract
{
    /**
     * @desc Returns the common headers for this application
     *
     * @return output
     */
    public function headers()
    {
        $view = $this->view;

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
}
