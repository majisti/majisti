<?php

/**
 * TODO: doc
 * TODO: add styles on each links generated
 * 
 * @author Steven Rosato
 */
class Majisti_View_Helper_EnumerateLanguagesAsLinks extends Zend_View_Helper_Abstract 
{	
	private $_firstLinkPassed = false;
	
	private $_separator;
	private $_accessor;
	
	private $_i18n;
	
	public function enumerateLanguagesAsLinks($showCurLang = false, $accessor = 'index/index/', $separator = '&nbsp;|&nbsp;')
	{
		if (null === $accessor) {
			// get accessor from current url
			$request = Zend_Controller_Front::getInstance()->getRequest();
			$accessor = ('default' !== strtolower($request->getModuleName()) ? $request->getModuleName() . '/' : '')
					 . $request->getControllerName() . '/'
					 . $request->getActionName() . '/';
		}
		
		$params = $request->getParams();
		unset($params['module'], $params['controller'], $params['action']);
		
		foreach ($params as $paramKey => $paramValue) {
			$accessor .= $paramKey . '/' . $paramValue . '/';
		}
		
		$this->_separator 	= $separator;
		$this->_accessor 	= $accessor . 'lang';
		
		$html = '';
		
		$this->_i18n = Zend_Registry::get('Majisti_I18n');
		$supportedLocales = $this->_i18n->getSupportedLocales(true);
		$currentLocale = $this->_i18n->getCurrentLocale(true);
		
		if( count($supportedLocales) ) {
			$html = '<div class="enumerated_languages">';
			
			if( $showCurLang ) {
				$html .= $this->_createLink(key($currentLocale), current($currentLocale));
			}
			
			foreach ($supportedLocales as $key => $value) {
				$html .= $this->_createLink($key, $value);
			}
			
			$html .= '</div>';
		}

		return $html; 
	}
	
	private function _createLink($abreviate, $linkName)
	{
		if( $this->_i18n->getCurrentLocale() == $abreviate ) {
			$link = $linkName;
		} else {
			$link = '<a href="' 
				. APPLICATION_URL 
				. "/{$this->_accessor}/" 
				. $abreviate 
				//. '?forward=' . urlencode($_SERVER['REQUEST_URI'])
				. '">' 
				. $linkName . '</a>';	
		}
		
		if( !$this->_firstLinkPassed ) {
			$this->_firstLinkPassed = true;
		} else {
			return $this->_separator . $link;
		}
		
		return $link;
	}
}