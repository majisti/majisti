<?php

/**
 * @desc The Anato's list of services. Most services
 * are rendered the same way hence the default partial
 * used for numerous actions.
 * 
 * @author Steven Rosato
 */
class ServicesController extends Zend_Controller_Action
{
	/** @var Anato_Services */
	private $_mServices;
	
	/**
	 * @desc Inits the navigation and the services model
	 */
	public function init()
	{
		$this->_initThirdNavigation();
		
		$this->_mServices = Anato_Center::getInstance()->getServicesModel();
	}
	
	/**
	 * @desc Always render a repetitive footer for all the services
	 */
	public function postDispatch()
	{
		$this->render('footer');
	}
	
	/**
	 * @desc The top page for this controller
	 */
	public function indexAction()
	{
		$this->_redirect('services/anatotherapy');
	}
	
	/**
	 * @desc This action renders just like the
	 * anatotherapy/treated-problem action
	 */
	public function anatotherapyAction() 
	{
		$this->render();
	}
	
	/*
	 * All those actions render the same. TemplateForward action helper
	 * was not used since we have a jQuery script listening on the current
	 * action name and therefore we simplify the process by just adding the
	 * needed actions here 
	 */
	public function anatoSweepingAction() { $this->_defaultServiceRender(); }
	public function anatoMassageAction() { $this->_defaultServiceRender(); }
	public function handsFeetBathAction() { $this->_defaultServiceRender(); }
	public function therapeuticBathAction() { $this->_defaultServiceRender(); }
	public function seaweedWrappingAction() { $this->_defaultServiceRender(); }
	public function colonicIrrigationAction() { $this->_defaultServiceRender(); }
	public function massotherapyAction() { $this->_defaultServiceRender(); }
	public function reflexologyAction() { $this->_defaultServiceRender(); }
	public function dutchSaunaAction() { $this->_defaultServiceRender(); }
	public function ionicSpaAction() { $this->_defaultServiceRender(); }
	
	/* custom render */
	public function feesAction() { $this->render(); }
	public function packagesAction() { $this->render(); }
	
	/**
	 * @desc This is how most services need to render
	 */
	private function _defaultServiceRender()
	{
		$this->view->service = $this->_mServices->{$this->_request->getActionName()};
		$this->render('default-service');
	}
	
	/**
	 * @desc Initializes the third navigation
	 */
	private function _initThirdNavigation()
	{
		$this->view->placeholder('third-navigation')->exchangeArray(
			Anato_Navigation::getInstance()->getThirdNavigation(Anato_Navigation::SERVICES)
		);
	}
}