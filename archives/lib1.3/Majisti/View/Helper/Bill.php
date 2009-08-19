<?php
class Majisti_View_Helper_Bill extends Zend_View_Helper_Abstract
{
	private $_key = 'Majisti_Shopping_Bill_Session';
	private $_tps = 5;
	private $_tvq = 7.5;
	
	public function bill( $sessionKey = null )
	{
		if( null === $sessionKey ) {
			return $this;
		}
		return new Zend_Session_Namespace( $sessionKey );
	}
	
	public function isTransactionExpired()
	{
		$bill = $this->bill($this->_key);

		if( $bill->expirationDelay != null ) {
			$delay = new Zend_Date($bill->expirationDelay);
			return time() > ($bill->creationTime + $delay->getTimestamp());
		}
		return false;
	}
	
	/**
	 * Enter description here...
	 * @pre isTransactionExpired()
	 * @return date|integer
	 */
	public function getRemainingTime()
	{
		/* Building limit date by summing expiration delay and creation time */
		$limit = new Zend_Date($this->bill($this->_key)->expirationDelay + $this->bill($this->_key)->creationTime);
		
		if( !$this->isTransactionExpired() ) {
			
			/* Remaining time = Limit date - now */
			$remaining = new Zend_Date($limit->sub(Zend_Date::now()));
			
			/* Building remaining time in a i:s format */
			return $remaining->get(Zend_Date::MINUTE) . ":" . $remaining->get(Zend_Date::SECOND);
		}
	}
	
	public function isLocked()
	{
		return $this->bill($this->_key)->locked;
	}
	
	public function getTotal($subTotal, $includeTax = true)
	{
		if( $includeTax ) {
			$subTotal += $this->getTax( $subTotal, $this->_tps );
			$subTotal += $this->getTax( $subTotal, $this->_tvq );
		}
		return round($subTotal, 2);
	}
	
/**
	 * Calculates any tax on the bill based on the subTotal passed multiplied by the taxValue factor.
	 *
	 * @param double $subTotal
	 * @param double $taxValue The tax value in PERCENTAGE
	 * @return double tax value
	 */
	private function getTax($subTotal, $taxValue)
	{
		return $subTotal * ($taxValue / 100);
	}
	
	public function setKey($newKey)
	{
		$this->_key = $newKey;
	}
	
	public function getKey()
	{
		return $this->_key;
	}

}