<?php

namespace Majisti\Controller\Plugin;

/**
 * @desc Listens for a specific URL param (that was setup in the
 * I18n plugin configuration) and switches locale according to that parameter
 * 
 * @author Steven Rosato
 */
class I18n extends AbstractPlugin
{
    /**
     * @var \Majisti\I18n\I18n
     */
    protected $_i18nObject;
    
	/**
	 * @desc Switches to a specific locale according to a request parameter
	 * that was setup under plugins.i18n.requestParam.
	 * 
	 * @throws Exception if the plugins.i18n.requestParam was never
	 * setup in the configuration
	 */
    public function preDispatch(\Zend_Controller_Request_Abstract $request)
    {
         $i18n 		= $this->getI18nObject();
         $config 	= $this->getConfig();
         
         if( isset($config->plugins) && isset($config->plugins->i18n) ) {
             $config = $config->plugins->i18n;
             if( !isset($config->requestParam) ) {
             	throw new Exception("Request parameter is mandatory in the configuration");
             }
             
             if( $lang = $request->getParam($config->requestParam, false) ) {
             	if( $i18n->isLocaleSupported($lang) && $lang !== $i18n->getCurrentLocale() ) {
             		$i18n->switchLocale($lang);
             	}
             }
         }
    }
    
    /**
     * @desc Returns the I18n object
     * 
     * @return \Majisti\I18n\I18n
     */
    public function getI18nObject()
    {
        if( null === $this->_i18nObject ) {
            if( \Zend_Registry::isRegistered('Majisti_I18n') ) {
                $this->_i18nObject = \Zend_Registry::get('Majisti_I18n');
            } else {
                $this->_i18nObject = new \Majisti\I18n\I18n();
                \Zend_Registry::set('Majisti_I18n', $this->_i18nObject);
            }
            
        }
        
        return $this->_i18nObject;
    }
}
