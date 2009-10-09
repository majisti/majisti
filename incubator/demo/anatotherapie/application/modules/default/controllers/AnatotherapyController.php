<?php

/**
 * @desc The "what is anatotherapy" controller
 * 
 * @author Steven Rosato
 */
class AnatotherapyController extends Zend_Controller_Action
{
	/**
	 * @desc Inits the navigation
	 */
	public function init()
	{
		$this->_initThirdNavigation();
	}
	
	/*
	 * Content displayed in the views
	 */
	public function indexAction() {}
	public function simplifiedTechniqueAction() {}
	public function benefitsAction() {}
	public function treatedProblemsAction() {}
	
	/**
	 * @desc Initializes the third navigation
	 */
	private function _initThirdNavigation()
	{
		$this->view->placeholder('third-navigation')->exchangeArray(
			Anato_Navigation::getInstance()->getThirdNavigation(Anato_Navigation::ANATOTHERAPY)
		);	
	}
}