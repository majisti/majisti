<?php

/**
 * @desc The HeadLink class is basically the same as Zend with the exception
 * that it can bundle stylesheets and compress them using a compression
 * handler.
 *
 * @author Majisti
 */

use Majisti\View\Helper\Head as Head;

class Majisti_View_Helper_HeadLink extends \Zend_View_Helper_HeadLink
{
    /**
     * @desc Bundles all the stylesheets contained in this HeadLink.
     *
     * @param string $path The path for the master file
     * @param string $url The url for the master file
     * @param IMinifier $minifier [optionnal] The bundler
     */
    public function minify($path, $url, Head\IMinifier $minifier = null)
    {
        if( null == $minifier ) {
            $minifier = new Head\StylesheetBundler();
        }

        $minifier->minify($this, $path, $url);
    }
}
