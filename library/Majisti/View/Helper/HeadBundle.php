<?php

class Majisti_View_Helper_HeadBundle extends \Zend_View_Helper_Placeholder_Container_Standalone
{
    /**
     * @var \Zend_View_Helper_HeadLink
     */
    protected $_bundles;

    /**
     * @var \Majisti\Util\Compression\IHandler
     */
    protected $_compressionHandler;

    protected $_bundlingEnabled = null;

    /**
     * @var bool
     */
    protected $_useCompression;

    public function headBundle($name, $func = 'appendStylesheet', $args = null)
    {
        if( !array_key_exists($name, $this->_bundles) ) {
            $this->_bundles[$name] = new self();
        }

        $headLink = $this->_bundles[$name];
        call_user_func_array(array($headLink, $func), $args);
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
        $content = parent::__toString();

        foreach ($this->getBundles() as $bundle) {
        	$content .= $bundle->__toString();
        }
    }
}