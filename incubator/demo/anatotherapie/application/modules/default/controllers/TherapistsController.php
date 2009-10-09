<?php

/**
 * @desc Controller for handling the anato therapists from the model.
 *
 * @author Steven Rosato
 */
class TherapistsController extends Zend_Controller_Action 
{
	/**
	 * @var Anato_Therapists 
	 */
	private $_mTherapists;
	
	/**
	 * @var Anato_Regions
	 */
	private $_mRegions;

	/**
	 * @desc Inits the model
	 */
	public function init()
	{
		$this->_mTherapists = Anato_Center::getInstance()->getTherapistsModel();
		$this->_mRegions 	= Anato_Center::getInstance()->getRegionsModel();
	}
	
	/**
	 * @desc Displays all regions and their therapists
	 */
	public function indexAction()
	{
		$this->view->regions = $this->_mRegions;
		
		/* make this view scrollable with the jQuery plugin */
		$this->view->headScript()->appendFile(LIB_URL . '/libraries/jquery/plugins/scrollTo.js');
		$this->view->headScript()->appendFile(BASE_URL . '/scripts/scrollTo.js');
	}
	
	/**
	 * @desc View a specific therapist
	 * 
	 * @param id The therapist's id
	 */
	public function viewAction()
	{
		if( $id = $this->_getParam('id', false) ) {
			$this->view->therapist = $this->_mTherapists->getById($id);
		}
	}
}