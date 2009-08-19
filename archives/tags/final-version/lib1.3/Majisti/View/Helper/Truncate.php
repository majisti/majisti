<?php

/**
 * TODO: doc
 * 
 * @author Steven Rosato
 */
class Majisti_View_Helper_Truncate extends Zend_View_Helper_Abstract
{
	/**
	 * Truncate a string at the given length and append the given trailling string, removing any punctuations
	 * given at the string's end.
	 *
	 * @param String $str The string to truncate
	 * @param Integer $length At what length should it begins truncating
	 * @param String $trailingString (optionnal) Default: ... Trailing string, ex: etc...
	 * @param String $punctuationRemoved (optionnal) Default: .!?:;,- The punctuations to remove for the ending characters.
	 * @return String The new truncated string
	 * 
	 * @author alishahnovin@hotmail.com http://ca.php.net/substr_replace
	 * @author Steven Rosato
	 */
	function truncate( $str, $length, $trailingString = '...', $punctuationRemoved = '.!?:;,-' )
	{
		$str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');

		if( mb_strlen($str) > $length ) {
			$str = mb_substr($str, 0, $length);
			$str = mb_substr($str, 0, strrpos($str, ' '));
	
			$str = (strspn(strrev($str), $punctuationRemoved) != 0) ? substr($str, 0, - strspn(strrev($str), $punctuationRemoved)) : $str;
			
			$str = $str . $trailingString;
		}

		//$str = htmlentities($str, ENT_QUOTES);
		return $str;
	}	
}
