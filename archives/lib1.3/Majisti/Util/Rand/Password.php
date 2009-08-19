<?php

/**
 * @desc CLASS NOT COMPLETE YET! Currently generates a 8 character long password
 * 
 * @author Steven Rosato
 */
class Majisti_Util_Rand_Password
{
	protected $_options;
	
	protected $_length = 8;
	
	public function __construct($options = array())
	{
		$this->_options = $options;
	}
	
	public function randomize()
	{
		return $this->_generatePassword();
	}
	
	/**
	 * THIS IS TEMPORARY! Code taken from
	 * http://www.laughing-buddha.net/jon/php/password/
	 * 
	 * A complete new algorithm based on KeePass software
	 * is in the roadmap
	 * 
	 * @param int $length[optional, def = 8] Password length
	 */
	private function _generatePassword ($length = 8)
	{
		// start with a blank password
		$password = "";
		
		// define possible characters
		$possible = "0123456789bcdfghjkmnpqrstvwxyz"; 
		
		// set up a counter
		$i = 0; 
		
		// add random characters to $password until $length is reached
		while ($i < $length) { 
			// pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			
			// we don't want this character if it's already in the password
			if (!strstr($password, $char)) { 
				$password .= $char;
				$i++;
			}
		}
	
		// done!
		return $password;
	}

}
