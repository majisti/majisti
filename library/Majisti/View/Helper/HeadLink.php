<?php

/**
 * @desc The HeadLink class is basically the same as Zend with the exception
 * that it can bundle stylesheets and optimize them using a optimize
 * handler.
 *
 * @author Majisti
 */

use Majisti\View\Helper\Head as Head;

class Majisti_View_Helper_HeadLink extends \Zend_View_Helper_HeadLink
{
    protected $_optimizer;

    /**
     * @desc Bundles and minifies all the stylesheets contained in this HeadLink.
     *
     * @param string $path The path for the master file
     * @param string $url The url for the master file
     * @param IMinifier $optimizer [optionnal] The bundler
     */
    public function optimize($path, $url, Head\IOptimizer $optimizer = null)
    {
        if( null === $optimizer ) {
            $optimizer = $this->getDefaultOptimizer();
        }

        $optimizer->optimize($path, $url);
    }

    public function bundle($path, $url, Head\IOptimizer $optimizer = null)
    {
        if( null === $optimizer ) {
            $optimizer = $this->getDefaultOptimizer();
        }

        $optimizer->bundle($path, $url);
    }

    public function minify(Head\IOptimizer $optimizer = null)
    {
        if( null === $optimizer ) {
            $optimizer = $this->getDefaultOptimizer();
        }

        $optimizer->minify();
    }

    public function getDefaultOptimizer()
    {
        if( null == $this->_optimizer ) {
            $this->_optimizer = new Head\HeadLinkOptimizer($this);
        }

        return $this->_optimizer;
    }
}
