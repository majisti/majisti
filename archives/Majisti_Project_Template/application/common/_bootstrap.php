<?php

require_once '_remote.php';

class ProjectName_Bootstrap extends Majisti_Bootstrap
{
	private static $_instance;

	protected function __construct() { parent::__construct(); }

	public function postConstruct()
	{
		$this->_applyConfiguration(
			new Majisti_Config_Xml( dirname(__FILE__) . '/config.xml',
			ENVIRONMENT_MODE,
			true)
		);
	}

	public static function getInstance()
	{
		if( self::$_instance == null ) {
			self::$_instance = new ProjectName_Bootstrap();
		}
		return self::$_instance;
	}
}