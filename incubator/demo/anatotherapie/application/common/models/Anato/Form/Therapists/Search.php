<?php

class Anato_Form_Therapists_Search extends Majisti_Form
{
	public function init()
	{
		$t = $this->getTranslator();
		
		$select_type = new Zend_Form_Element_Select('type');
		$select_type
			->addMultiOptions(array(
				'fName' 	=> $t->_('Prénom'),
				'name' 		=> $t->_('Nom'),
				'email' 	=> $t->_('Courriel')
			))
			->setValue('fName')
			->setLabel($t->_('Filtre:'))
		;
		
		$tf_keyword = new Zend_Form_Element_Text('keyword');
		$tf_keyword
			->setLabel($t->_('Mot-clé'))
			->setRequired(true)
		;
		
		$btn_submit = new Zend_Form_Element_Submit('btn_submit');
		$btn_submit->setLabel($t->_('Rechercher'));
		
		$this->setAction( BASE_URL . '/admin/therapists/list/' );
		
		$this->setMethod('get');
		
		$this->addElements(array(
			$select_type,
			$tf_keyword,
			$btn_submit
		));
	}
}