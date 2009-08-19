<?php

/**
 * TODO: doc
 * Registers a paginator to a controller's view
 *
 * @param Zend_Controller_Action $controller
 * @param Array $items
 * @param Integer $countPerPage (optionnal) Count per page. Default is 10
 * @param String $defaultPath
 * @param String $paramName
 * @param String $mode(optionnal) Default Sliding
 *
 * @author Steven Rosato
 *
 * @return Zend_Paginator $paginator
*/
class Majisti_Controller_Action_Helper_RegisterPaginator extends Zend_Controller_Action_Helper_Abstract
{
	public function direct(
		$items,
		$countPerPage = 10,
		$defaultPath = 'default_pagination_control.phtml',
		$paramName = 'page',
		$mode = 'Sliding'
	)
	{
		return $this->_registerPaginator($items, $countPerPage, $defaultPath, $paramName, $mode);
	}

	private function _registerPaginator($items, $countPerPage, $defaultPath, $paramName, $mode)
	{
		$controller = $this->getActionController();

		$paginator = Zend_Paginator::factory($items);
		$paginator->setItemCountPerPage($countPerPage);
		$paginator->setCurrentPageNumber($controller->getRequest()->get($paramName));

		Zend_Paginator::setDefaultScrollingStyle($mode);

		if( Zend_Layout::getMvcInstance() != null ) {
			$controller->view->addScriptPath(Zend_Layout::getMvcInstance()->getLayoutPath());
		}

		Zend_View_Helper_PaginationControl::setDefaultViewPartial($defaultPath);
		$paginator->setView($controller->view);

		// TODO : deprecate this
		$controller->view->paginator = $paginator;

		return $paginator;
	}
}