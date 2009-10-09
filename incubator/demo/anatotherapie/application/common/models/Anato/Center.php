<?php

/**
 * @desc This is the facade model which will compose every
 * models needed for this singleton class. Every model
 * are lazily instanciated.
 * 
 * @author Steven Rosato
 */
class Anato_Center
{
	/** @var Anato_Center */
	private static $_instance;
	
	/** @var Anato_Therapy */
	private $_mTherapy;
	
	/** @var Anato_Comments */
	private $_mComments;
	
	/** @var Anato_Therapists */
	private $_mTherapists;
	
	/** @var Anato_Regions */
	private $_mRegions;
	
	/** @var Anato_TherapistsRegions */
	private $_mTherapistRegions;

	/** @var Anato_Services */
	private $_mServices;
	
	/** @var Anato_Trainings */
	private $_mTrainings;
	
	/** @var Anato_History */
	private $_mHistory;
	
	/**
	 * @desc Constructs this composite model
	 */
	private function __construct() 
	{}
	
	/**
	 * @return Anato_Center
	 */
	public static function getInstance()
	{
		if( null === self::$_instance ) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * @return Anato_Therapy
	 */
	public function getTherapyModel()
	{
		if( null === $this->_mTherapy ) {
			$this->_mTherapy = new Anato_Therapy();
		}
		
		return $this->_mTherapy;
	}
	
	/**
	 * @return Anato_Comments
	 */
	public function getCommentsModel()
	{
		if( null === $this->_mComments ) {
			$this->_mComments = new Anato_Comments();
		}
		
		return $this->_mComments;
	}
	
	/**
	 * @return Anato_Regions
	 */
	public function getRegionsModel()
	{
		if( null === $this->_mRegions ) {
			$this->_mRegions = new Anato_Regions();
		}
		
		return $this->_mRegions;
	}
	
	/**
	 * @return Anato_Therapists
	 */
	public function getTherapistsModel()
	{
		if( null === $this->_mTherapists ) {
			$this->_mTherapists = new Anato_Therapists();
		}
		
		return $this->_mTherapists;
	}
	
	/**
	 * @return Anato_TherapistsRegions
	 */
	public function getTherapistRegionsModel()
	{
		if( null === $this->_mTherapistRegions ) {
			$this->_mTherapistsRegions = new Anato_TherapistsRegions();
		}
		
		return $this->_mTherapistsRegions;
	}
	
	/**
	 * @return Anato_Services
	 */
	public function getServicesModel()
	{
		if( null === $this->_mServices ) {
			$this->_mServices = new Anato_Services();
		}
		
		return $this->_mServices;
	}
	
	/**
	 * @return Anato_Trainings
	 */
	public function getTrainingsModel()
	{
		if( null === $this->_mTrainings ) {
			$this->_mTrainings = new Anato_Trainings();
		}
		
		return $this->_mTrainings;
	}
	
	/**
	 * @return Anato_History
	 */
	public function getHistoryModel()
	{
		if( null === $this->_mHistory ) {
			$this->_mHistory = new Anato_History();
		}
		
		return $this->_mHistory;
	}
}