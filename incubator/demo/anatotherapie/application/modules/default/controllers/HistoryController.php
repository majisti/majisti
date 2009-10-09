<?php

/**
 * @desc Displays the Anato's History
 *
 * @author Steven Rosato
 */
class HistoryController extends Zend_Controller_Action
{
	/** @var Anato_History */
	private $_mHistory;
	
	/**
	 * @desc Initialises the navigation and the model
	 */
	public function init()
	{
		$this->_initThirdNavigation();
		
		$this->_mHistory = $this->view->history = Anato_Center::getInstance()->getHistoryModel();
	}

	/*
	 * Content actions
	 */
	public function indexAction() {}
	public function aboutTherapyAction() {}
	public function discoveryAction() {}
	public function professionalTrainingAction() {}
	public function otherInformationAction() {}
	
	public function founderBiographyAction() 
	{
		/* make this view scrollable with the jQuery plugin */
		$this->view->headScript()->appendFile(LIB_URL . '/libraries/jquery/plugins/scrollTo.js');
		$this->view->headScript()->appendFile(BASE_URL . '/scripts/scrollTo.js');
	}
	
	/**
	 * @desc Initializes the third navigation
	 */
	private function _initThirdNavigation()
	{
		$this->view->placeholder('third-navigation')->exchangeArray(
			Anato_Navigation::getInstance()->getThirdNavigation(Anato_Navigation::HISTORY)
		);
	}
}