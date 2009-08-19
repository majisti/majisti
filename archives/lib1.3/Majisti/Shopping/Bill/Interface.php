<?php

/**
 * TODO: doc
 *
 * @author Steven Rosato
 */
interface Majisti_Shopping_Bill_Interface
{
	/**
	 * @return int
	 */
	public function getTransactionId();
	
	public function checkout();
	
	/**
	 *
	 * @param mixed $items instance or array containing multiple instance of Majisti_Shopping_Cart_Item
	 */
	public function addItems($items = array());
	
	public function setPaymentGateway(Majisti_Shopping_Gateway_Interface $gateway);
}