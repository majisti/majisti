<?php

/**
 * TODO: doc
 * 
 * @author Steven Rosato
 */
class Majisti_View_Helper_GenerateTitle extends Zend_View_Helper_Abstract
{
	public function generateTitle($prefix, $useAction = true, $useController = false, $useModule = false, $separator = ' :: ')
	{
		$title = $prefix;
		
		$request = Zend_Controller_Front::getInstance()->getRequest();
			
		if( $useModule ) {
			$title .= $separator;
			$title .= $request->getModuleName();
		}
		
		if( $useController ) {
			$title .= $separator;
			$title .=  $request->getControllerName();
		}
		
		if( $useAction ) {
			$title .= $separator;
			$title .=  $request->getActionName();
		}
		
		return $title;
	}
}