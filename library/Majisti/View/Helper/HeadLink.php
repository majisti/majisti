<?php

class Majisti_View_Helper_HeadLink extends \Zend_View_Helper_HeadLink
{
    /**
     * @var \Zend_View_Helper_HeadLink
     */
    protected $_bundles = array();

    /**
     * @var \Majisti\Util\Compression\IHandler
     */
    protected $_compressionHandler;

    protected $_bundlingEnabled = null;

    /**
     * @var bool
     */
    protected $_useCompression;

    public function headBundle($name = null)
    {
        if( null === $name ) {
            return $this;
        }

        return $this->getBundle($name);
    }

    public function getBundle($name)
    {
        $bundles = $this->getBundles();
        if( !array_key_exists($name, $bundles) ) {
            throw new Exception("Bundle {$name} is inexistant");
        }

        return $bundles[$name][0];
    }

    public function appendBundle($name, $type, $filePath)
    {
        $bundles = $this->getBundles();

        if( array_key_exists($name, $bundles) ) {
           throw new Exception("Bundle $name already exists");
        }

        $bundles[$name] = array(new $type(), $filePath);
        $this->_bundles = $bundles;
    }

    public function isBundlingEnabled()
    {
        if( null == $this->_bundlingEnabled ) {
            $this->_bundlingEnabled = defined('APPLICATION_ENVIRONMENT')
                && 'production' === APPLICATION_ENVIRONMENT
                || 'staging' === APPLICATION_ENVIRONMENT;
        }

        return $this->_bundlingEnabled;
    }

    public function setBundlingEnabled($flag = true)
    {
        $this->_bundlingEnabled = (bool) $flag;
    }

    public function setCompressionHandler(\Majisti\Util\Compression\IHandler $handler)
    {

    }

    public function setUseCompression($flag = true)
    {
        $this->_useCompression = (bool) $flag;
    }

    public function getBundles()
    {
        return $this->_bundles;
    }

    public function __toString()
    {
        $headLink = new \Zend_View_Helper_HeadLink();

        $content = '';
        foreach ($headLink as $head) {
            $content .= file_get_contents($head->href);
        }

        $bundles = $this->getBundles();

        file_put_contents($bundles['themeStyles'][1], $content);

        /* reset headLink */
        $headLink->exchangeArray(array());

        $headLink->appendStylesheet($bundles['themeStyles'][1]);

        return $headLink->__toString();

        \Zend_Debug::dump($headLink->__toString());
        exit;

        $content = '';
        foreach ($this->getBundles() as $bundle) {
            $head = (array)$bundle[0]->getContainer();
        	\Zend_Debug::dump($head[0]);
            exit;
        }

        return $content;
    }
}