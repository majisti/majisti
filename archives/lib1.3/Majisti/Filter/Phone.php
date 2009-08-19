<?php

/**
 * TODO: doc
 * 
 * @author Jean-François Hamelin
 */
class Majisti_Filter_Phone implements Zend_Filter_Interface
{
    public function filter($phone)
    {
		$phone = ereg_replace( "[^0-9]", "", $phone ); // Strip out non-numerics
		if( ereg( "^([0-9]{3})([0-9]{3})([0-9]{4})$", $phone, $NumberParts ) ){                                  
        	return "(" . $NumberParts[1] . ") " . $NumberParts[2] . "-" . $NumberParts[3];
		}else{
			return $phone;
		}
        
    }
}

