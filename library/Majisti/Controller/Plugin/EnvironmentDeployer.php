<?php

namespace Majisti\Controller\Plugin;

class EnvironmentDeployer extends AbstractPlugin
{
    public function preDispatch(\Zend_Controller_Request_Abstract $request)
    {

    }

    protected function getHeadLink()
    {
        return new \Zend_View_Helper_HeadLink();
    }

    public function bundleCss($path, $href, $versionize = true, $extras = array())
    {
        $headLink = $this->getHeadLink();

        //FIXME: what about media type ???

        $keptLinks = array();
        $content = '';
        foreach($headLink as $link) {
            if( 'stylesheet' === $link->rel && !$link->conditionalStylesheet ) {
                $content .= file_get_contents($link->href);
            } else {
                $keptLinks[] = $link;
            }
        }

        if( $handle = fopen($path, 'w') ) {
            fwrite($handle, $content);
            fclose($handle);
        }

        $headLink->exchangeArray($keptLinks);

        $headLink->appendStylesheet($href, 'screen', null, $extras);
    }

    public function compressCss()
    {

    }

    static public function getDefaultCompressor()
    {

    }

    static public function setDefaultCompressor(Majisti\Util\Compression\ICompressor $compressor)
    {

    }
}
