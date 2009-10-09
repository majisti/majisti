<?php

/**
 * @desc Anato_Comments listing
 * 
 * @author Steven Rosato
 */
class CommentsController extends Zend_Controller_Action
{
	/** @var Anato_Comments */
	private $_mComments;
	
	/**
	 * @desc Initializes the model
	 */
	public function init()
	{
		$this->_mComments = $this->view->comments = Anato_Center::getInstance()->getCommentsModel();	
	}
	
	/**
	 * @desc List the comments
	 */
	public function indexAction()
	{
	}
	
	/**
	 * @desc View a single comment
	 */
	public function viewAction()
	{
		if( $id = $this->_getParam('id', false) ) {
			$this->view->comment = $this->_mComments->getById($id);
		}
	}
}