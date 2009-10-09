<?php

/**
 * @desc This is the Therapists controller. Listing all therapists
 * along with the possibility to add/edit/list/delete/search them.
 * 
 * @author Steven Rosato and Jean-François Hamelin
 */
class Admin_TherapistsController extends Zend_Controller_Action
{
	/** For pagination */
	const THERAPISTS_PER_PAGE = 10;
	
	/** @var Anato_Therapists */
	private $_therapists;
	
	/** @var Zend_Session_Namespace */
	private $_session;
	
	/** @var Majisti_Config_Xml */
	private $_config;
	
	/**
	 * @desc Assigns the model to the instance's variable
	 */
	public function init()
	{
		$this->_therapists 		= Anato_Center::getInstance()->getTherapistsModel();
		$this->_config 			= Zend_Registry::get('config');
		$this->_session 		= new Zend_Session_Namespace($this->_config->session . "Admin");
	}
	
	/**
	 * @desc Proxies to {@link addAction()}
	 */
	public function indexAction()
	{
		$this->_redirect('admin/therapists/list');	
	}
	
	/**
	 * @desc Adds a therapist to the database using post.
	 */
	public function addAction()
	{
		/* form model */
		$this->view->form = $form = new Anato_Form_Therapists_Add();
		
		/* add therapist */
		if( $this->_manageTherapist($form) ) {
			$this->view->flashMessages($this->view->_('Le thérapeute à été ajouté avec succès'));
			$this->_redirect('admin/therapists/add');	
		}
	}
	
	/**
	 * @desc Updates or edit a therapist
	 *
	 * @param Zend_Form $form The form, which will be validated and queried on
	 * @param bool $update[opt] True if this is an update, false an insertion
	 * @param int $id [opt] The therapist id if it is an update.
	 * 
	 * @return True if the therapist was inserted/updated
	 */
	private function _manageTherapist(Zend_Form $form, $update = false, $id = null)
	{
		/* on post, validate and update/insert */	
		if( $this->_request->isPost() && $form->isValid($_POST) ) {
			
			/* get values and remove last element which is the submit */
			$values = $form->getValues();
			array_pop($values);
			$regions = $values['ms_therapists_regions'];
			
			/* Removing this value for insert purposes */
			unset($values['ms_therapists_regions']);

			$therapistsRegions = Anato_Center::getInstance()->getTherapistRegionsModel();
			
			/* insert */
			if( !$update ) {
				$this->_therapists->insert($values);
				$therapistsRegions->manageTherapistRegions(
					$this->_therapists->getLastInsertId(),
					$regions
				);
				$this->view->message = $this->view->_('Le thérapeute à été ajouté avec succès');
			} else { /* update */
				$this->_therapists->update($values, $id);
				$therapistsRegions->manageTherapistRegions($id, $regions);
				$this->view->flashMessages($this->view->_('Le thérapeute à été édité avec succès'));
			}
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * @desc Updates a therapist in the database using post
	 */
	public function editAction()
	{
		/* must provide id in url */
		$id = $this->_getParam('id', false);
		if( $id && $this->_therapists->has($id) ) {
			/* get form model, populate it and change submit's button label */
			$this->view->form = $form = new Anato_Form_Therapists_Add();
			$form->getElement('btn_submit')->setLabel($this->view->_('Editer'));
			
			/* populate therapist and it's associated regions */
			$form->populate($this->_therapists->getById($id)->toArray());
			
			$therapistsRegions = Anato_Center::getInstance()->getTherapistRegionsModel();
			$regions = array();
			foreach ($therapistsRegions->getAll(false, array('where' => "therapistId = {$id}")) as $assoc) {
				$regions[] = $assoc->regionId;
			}
			$form->populate(array('ms_therapists_regions' => $regions));
			
			$this->_manageTherapist($form, true, $id);
		}
		
		/* it is basically the same rendering as the addAction */
		$this->render('add');
	}
	
	/**
	 * @desc Deletes a therapist in the database using post. 
	 * 
	 * This is an AJAX action.
	 * 
	 * @param id The therapist's id to delete
	 */
	public function deleteAction()
	{
		$this->view->layout()->disableLayout();
		
		$id = $this->_getParam('id', false);
		
		if( $this->_request->isXmlHttpRequest() && $this->_request->isPost() && $id ) {
			$this->_therapists->deleteById($id);
		}
	}
	
	/**
	 * @desc Lists all therapists in table based html output using a paginator
	 * 
	 * Here are the list of available url's params:
	 * 
	 * @param keyword [optionnal][default = none] The keyword
	 * @param type [optionnal][default = none] The column that will serve for the search
	 */
	public function listAction()
	{
		/* append js file for pagination listing */
		$this->view->headScript()->appendFile(BASE_URL . '/scripts/admin/list.js');
		
		/* in case we have params, we narrow the results */
		$keyword 	= $this->_getParam('keyword', false);
		$by 		= $this->_getParam('type', false);
		$results 	= $keyword && $by ? $this->_therapists->search($keyword, $by) : $this->_therapists->getAll();
		
//		foreach ($results as $result) {
//			Zend_Debug::dump($result->email);
//			exit;
//		}
		/* do the listing */
		$this->view->listing()
			->set('Table', 'Therapists')
			->setObjectIterable($results)
			->setColumns(array(
				array(
					'name' => $this->view->_('Courriel'),
					'value' => '$email'
				),
				array(
					'name' => $this->view->_('Nom'),
					'value' => array('sprintf(=%s %s, $fName, $name)')
				),
				array(
					'name' => $this->view->_('Adresse'),
					'value' => '$address'
				),
				array(
					'name' => $this->view->_('Téléphone'),
					'value' => '$phone'
				),
				array(
					'name' => $this->view->_('Adresse privée'),
					'value' => '$addressIsPrivate'
				),
				array(
					'name' => $this->view->_('Opérations'),
					'value' => '$id',
					'partial' => 'therapists/operations.phtml'
				)
			))
		;
	}
	
	/**
	 * @desc Search within the therapists. 
	 * 
	 * This is an AJAX action.
	 *
	 * @param keyword
	 * @param by
	 */
	public function searchAction()
	{
		/* form model */
		$this->view->form = $form = new Anato_Form_Therapists_Search();
		
		/* append js file for searching with autocomplete */
		$this->view->headScript()->appendFile(BASE_URL . '/scripts/admin/search.js');
		
		/* AJAX Requests only */
		if( $this->_request->isXmlHttpRequest() ) {
			
			/* needed params */
			$keyword 	= $this->_getParam('q', false);
			$by 		= $this->_getParam('type', false);
			$limit 		= $this->_getParam('limit', 10);
			
			/* search and append url to the results */
			if( $keyword && $by ) {
				$results = $this->_therapists->search($keyword, $by, $limit)->toArray();
				foreach ( $results as &$result ) {
					$result['url'] = BASE_URL . '/admin/therapists/edit/id/' . $result['id'];
				}
				/* send in JSON encoded data */
				$this->_helper->json->sendJson($results);
			}
		}
	}
}