<?php

/**
 * @desc The website's navigation
 *
 * @author Steven Rosato
 */
class Anato_Navigation
{
	/* available pages */
	const ANATOTHERAPY 	= 'anatotherapy';
	const SERVICES 		= 'services';
	const TRAININGS		= 'trainings';
	const HISTORY		= 'history';
	
	/** @var Anato_ThirdNavigation */
	private static $_instance;
	
	/** @desc Singleton class */
	private function __construct() {}
	
	/** @var Array */
	protected $_anatotherapy = array(
		'default/anatotherapy/index' => array(
			'default/anatotherapy/index',  
			"Qu’est-ce que l’anatothérapie"
		),
		'default/anatotherapy/simplified-technique' => array(
			'default/anatotherapy/simplified-technique', 
			"L'anatothérapie en résumé"
		),
		'default/anatotherapy/benefits' => array(
			'default/anatotherapy/benefits', 
			"Les bienfaits"
		),
		'default/anatotherapy/treated-problems' => array(
			'default/anatotherapy/treated-problems', 
			"Problèmes de santé de A à Z pouvant être traités"
		)
	);
	
	protected $_history = array(
		'default/history/index' => array(
			'default/history/index',
			'Historique'
		),
		'default/history/discovery' => array(
			'default/history/discovery',
			"Découverte de l'anatothérapie"
		),
		'default/history/founder-biography' => array(
			'default/history/founder-biography',
			'Biographie de la fondatrice'
		),
		'default/history/professional-training' => array(
			'default/history/professional-training',
			'Formation de l’Institut'
		),
		'default/history/other-information' => array(
			'default/history/other-information',
			'Autres informations'
		),
	);
	
	/** @var Array */
	protected $_services;
	
	/** @var Array */
	protected $_trainings;
	
	/**
	 * @desc Will lazily instanciates the Anato's services
	 * based on the Anato_Services list model.
	 */
	private function _initServices()
	{
		$services = Anato_Center::getInstance()->getServicesModel();
		
		$this->_services = $this->_getListFromServiceModel($services, 'default/services/');
	}
	
	/**
	 * @desc Will lazily instanciates the Anato's trainings
	 * based on the Anato_Trainings list model
	 */
	private function _initTrainings()
	{
		$trainings = Anato_Center::getInstance()->getTrainingsModel();
		
		$this->_trainings = $this->_getListFromServiceModel($trainings, 'default/training/');
	}
	
	/**
	 * @desc Will return an array containing services from an Anato_Service.
	 * 
	 * Note: Anato_Training is an Anato_Service, hence this private function
	 *
	 * @param Iterable $servicesList A list of Anato_Service objects
	 * @param String $urlPrefix The url prefix
	 * 
	 * @return Array The list of services in an array
	 */
	private function _getListFromServiceModel($servicesList, $urlPrefix)
	{
		$array = new stdClass();
		
		foreach ($servicesList as $service) {
			$array->{$urlPrefix . $service->getKey()} = array($urlPrefix . $service->getKey(), $service->getName());
		}
		
		return (array)$array;
	}
	
	/**
	 * @return Anato_Navigation
	 */
	public static function getInstance()
	{
		if( null === self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * @desc Returns the third navigation as an array
	 *
	 * @param String $page The navigation page to fetch, use the class' constants
	 * @return Array The navigation pages
	 */
	public function getThirdNavigation($page)
	{
		$pageVar = '_' . $page;
		
		/* attempt to lazy instanciate if no items in array */
		if( !count($this->{$pageVar}) ) {
			$initFunction = '_init' . ucfirst($page);
			
			$this->{$initFunction}();
		}
		
		return $this->{$pageVar};
	}
}