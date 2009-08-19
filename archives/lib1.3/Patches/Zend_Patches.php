<?php

echo "Cannot execute this script!";
exit();

/**
 * List of all patches to apply on the lib if restore is needed.
 */



	// Zend version : 1.7.+
	// Description  : patch to add charset functionality to MySQLi adapater
	// Class        : Zend_Db_Adapter_Mysqli
	// function     : _connect()
	// line         : 338 to 346
	// Date         : 2009-03-04
	// Source       : http://framework.zend.com/issues/browse/ZF-1541
	if (array_key_exists('charset', $this->_config)) {
	   if(!$this->_connection->set_charset($this->_config['charset'])) {
	        /**
	        * @see Zend_Db_Adapter_Mysqli_Exception
	        */
	        require_once 'Zend/Db/Adapter/Mysqli/Exception.php';
	        throw new Zend_Db_Adapter_Mysqli_Exception('could not set charset');
	    }
	}



	// Zend version : 1.7.+
	// Description  : patch to fix the slider problem where it returns back to 0 upon 'change' event
	// Class        : ZendX_JQuery_Form_Element_Slider
	// function     : slider(...)
	// line         : 81
	// Date         : 2009-03-17
	// Source       : 
	$sliderUpdateFn .= sprintf('    %s("#%s").attr("value", %s("#%s-slider").slider("value"));'.PHP_EOL);