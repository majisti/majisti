<?php

/**
 * @desc This is the add (or edit) a therapist form
 * 
 * @author Steven Rosato and Jean-François Hamelin
 */
class Anato_Form_Therapists_Add extends Majisti_Form
{
	/** @var Anato_Regions */
	private $_regions;
	
	/**
	 * @desc Constructs the form
	 */
	public function __construct()
	{
		$this->_regions = Anato_Center::getInstance()->getRegionsModel();
		parent::__construct();
	}
	
	/**
	 * @desc Inits the form
	 */
	public function init()
	{
		/* get models */
		$t = $this->getTranslator();
		
		/* first name */
		$tf_fName = new Zend_Form_Element_Text('fName');
		$tf_fName
			->setLabel($t->_('Prénom: '))
			->setRequired(true)
		;
		
		/* last name */
		$tf_name = new Zend_Form_Element_Text('name');
		$tf_name
			->setLabel($t->_('Nom: '))
			->setRequired(true)
		;
		
		/* address */
		$tf_address = new Zend_Form_Element_Text('address');
		$tf_address
			->setLabel($t->_('Adresse: '))
			->setRequired(true)
		;
		
		/* mark as private address */
		$cb_addressIsPrivate = new Zend_Form_Element_Checkbox('addressIsPrivate');
		$cb_addressIsPrivate
			->setLabel($t->_('Adresse privée?'))
		;
		
		/* phone number */
		$tf_phone = new Zend_Form_Element_Text('phone');
		$tf_phone
			->setLabel($t->_('Numéro de téléphone (avec indicatif régional): '))
			->addFilter(new Majisti_Filter_Phone())
			->addValidator(new Majisti_Validate_Phone())
			->setRequired()
		;
		
		/* fax */
		$tf_fax = new Zend_Form_Element_Text('fax');
		$tf_fax
			->setLabel($t->_("Télécopieur:"));
		
		/* email */
		$tf_email = new Zend_Form_Element_Text('email');
		$tf_email
			->setLabel($t->_('Courriel: '))
			->setRequired(true)
			->addValidator(new Zend_Validate_EmailAddress())
		;
		
		/* regions */
		$select_regions = new Zend_Form_Element_Multiselect('ms_therapists_regions', array(
			'required'		=> false,
			'label'			=> $t->_('Région(s)') . ' :',
			'description' 	=> $t->_('Associer les régions disponibles (à droite) au thérapeute.'),
			'attribs'		=> array(
				'rel'	 		=> 'authors',
				'style' 		=> 'width:600px; height:400px;'
			),
			'registerInArrayValidator' => false, /* do not check if the value is possible, just suppose it is */
			'multiOptions' => $this->_getRegions()	
        ));
		
        /* more infos */
		$ta_misc = new Zend_Form_Element_Textarea('infos');
		$ta_misc
			->setLabel($t->_('Autres informations:'))
			->setAttribs(array(
				'cols' => 50,
				'rows' => 10
			))
		;
			
		/* submit button */
		$btn_submit = new Zend_Form_Element_Submit('btn_submit', $t->_('Ajouter'));
		
		/* add elements */
		$this->addElements(array(
			$tf_fName,
			$tf_name,
			$tf_address,
			$cb_addressIsPrivate,
			$tf_phone,
			$tf_fax,
			$tf_email,
			$select_regions,
			$ta_misc,
			$btn_submit
		));	
		
		$this->setName('formAddTherapist');
		$this->filterName('btn_submit');
	}
	
	/**
	 * @desc Return the Anato regions
	 *
	 * @return Array
	 */
	private function _getRegions()
	{
		$rowset = $this->_regions->getAll();
		$return = array();
		
		foreach ($rowset as $row) {
			$return[$row->id] = $row->name;
		}
		return $return;
	}
}