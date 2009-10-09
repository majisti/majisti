<?php

/**
 * @desc This is the training controller. It basically renders
 * the same way as the services controller
 *
 * @author Steven Rosato
 */
class TrainingController extends Zend_Controller_Action
{
	/** @var Anato_Trainings */
	private $_mTrainings;
	
	/**
	 * @desc Init the navigation and the model
	 */
	public function init()
	{
		$this->_mTrainings = Anato_Center::getInstance()->getTrainingsModel();
		
		$this->_initThirdNavigation();
	}
	
	/** @desc The index page */
	public function indexAction()
	{
		$this->_redirect('training/corporate');
	}
	
	/*
	 * All those actions render the same. TemplateForward action helper
	 * was not used since we have a jQuery script listening on the current
	 * action name and therefore we simplify the process by just adding the
	 * needed actions here 
	 */
	public function corporateAction() { $this->_defaultServiceRender(); }
	public function teachingAction() { $this->_defaultServiceRender(); }
	public function trainingAction() { $this->_defaultServiceRender(); }
	public function anatotherapyAction() { $this->_defaultServiceRender(); }
	public function massotherapyAction() { $this->_defaultServiceRender(); }
	public function reflexologyAction() { $this->_defaultServiceRender(); }
	public function certificationAction() { $this->_defaultServiceRender(); }
	public function professionAction() { $this->_defaultServiceRender(); }
	
	/**
	 * @desc This is how most trainings need to render (they render the same
	 * as the services)
	 */
	private function _defaultServiceRender()
	{
		$this->view->service = $this->_mTrainings->{$this->_request->getActionName()};
		$this->renderScript('services/default-service.phtml');
	}
	
	/**
	 * @desc Initializes the third navigation
	 */
	private function _initThirdNavigation()
	{
		$this->view->placeholder('third-navigation')->exchangeArray(
			Anato_Navigation::getInstance()->getThirdNavigation(Anato_Navigation::TRAININGS)
		);
	}
}