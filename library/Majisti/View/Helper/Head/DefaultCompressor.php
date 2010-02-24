<?php

namespace \Majisti\View\Helper\Head;

use \Majisti\Util\Compression as Compression;

class DefaultCompressor extends AbstractMinifier implements ICompressor
{
    /**
     * @var Compression\ICompressor The compressor
     */
    protected $_compressionHandler;

    public function setCompressionHandler(
        Compression\ICompressor $compressor)
    {

    }

    public function getCompressionHandler()
    {
        if( null === $this->_compressionHandler ) {
            $this->_compressionHandler = new Compression\Yui();
        }

        return $this->_compressionHandler;
    }

    public function setCompressionEnabled()
    {

    }

    public function isCompressionEnabled()
    {

    }

    public function compress($header, $path)
    {
       
    }

    protected function compressCss($header, $path)
    {

    }

    protected function compressJs($header, $path)
    {

    }
}
