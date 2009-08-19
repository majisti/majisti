<?php

/**
 * Helper method to return the instance of the cart
 *
 * To use this helper, the shopping cart should have been assigned
 * a cart factory.
 *
 * @author Yanick Rochon
 */

class Majisti_View_Helper_Cart extends Zend_View_Helper_Abstract
{
	public function cart()
	{
		return Majisti_Shopping_Cart::getInstance();
	}
}