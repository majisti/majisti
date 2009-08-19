<?php

/**
 * Shows a breadcrumb trail
 * TODO: doc
 * 
 * @author Steven Rosato
 */
class Majisti_View_Helper_Breadcrumbs extends Zend_View_Helper_Abstract
{
	public function breadcrumbs(array $options = array())
	{
		$request = Zend_Controller_Front::getInstance()->getRequest();
		
		/* defaults */
		if( !isset($options['here']) ) {
			$options['here'] = 'You are here:';
		}
		
		if( !isset($options['showModule']) ) {
			$options['showModule'] = true;
		}
		
		if( !isset($options['showController']) ) {
			$options['showController'] = true;
		}
		
//		if( !isset($options['showParams']) ) {
//			$options['showParams'] = true;
//		}
		
		if( !isset($options['delimiter']) ) {
			$options['delimiter'] = '&nbsp;&gt;&nbsp;';
		}
		
		/* delimiter */
		$options['delimiter'] = '<span class="breadcrumb_delimiter">' . $options['delimiter'] . '</span>';
		
		/* breadcrumb trail start */
		$html = '<div class="breadcrumbs"><span class="breadcrumb_here">' . $options['here'] . '</span> ';
		
		/* show module */
		if( $options['showModule'] ) {
			$html .= '<span class="breadcrumb_module">';
			
			if( $request->getControllerName() != 'index' ) {
				$html .= '<a href="'. $this->view->baseUrl() . '/' 
					. $request->getModuleName() . '">' 
					. $request->getModuleName() 
					. '</a>';
			} else {
				$html .= $request->getModuleName();
			}
			
			$html .= '</span>' . $options['delimiter'];
		}
			
		/* show controller */
		if( $options['showController'] ) {
			$html .= '<span class="breadcrumb_controller">';
			
			if( $request->getActionName() != 'index' ) {
				$html .= '<a href="'. $this->view->baseUrl() . '/' 
				. $request->getControllerName() . '">' 
				. $request->getControllerName() 
				. '</a>';
			} else {
				$html .= $request->getControllerName();
			}
			
			$html .= '</span>' . $options['delimiter'];
		}
			
		/* show action */
		$html .= '<span class="breadcrumb_action">' . $request->getActionName() . '</span></div>';
	
		return $html;
	}
}