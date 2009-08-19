<?php

/**
 * TODO: doc
 * 
 * @author Steven Rosato
 */
class Majisti_View_Helper_RedirectionLink extends Zend_View_Helper_Abstract
{
	public function redirectionLink($link, $prependBaseUrl = true)
	{
		$uri = "";
		if( $prependBaseUrl ) {
			$uri .= Zend_Controller_Front::getInstance()->getBaseUrl();
		}
	
		if( substr($uri, -1, 1) !== '/' ) {
			$uri .= '/';
		}
		
		$uri .= $link;
		
		if( substr($uri, -1, 1) !== '/' ) {
			$uri .= '/';
		}
		
		$uri .= '?forward=' . urlencode($_SERVER['REQUEST_URI']);
		
		return $uri;
	}
}