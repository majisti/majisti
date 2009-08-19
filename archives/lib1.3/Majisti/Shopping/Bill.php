<?php
/**
 * TODO: doc
 *
 * @author Jean-François Hamelin
 */
class Majisti_Shopping_Bill implements Majisti_Shopping_Bill_Interface
{
	protected $_gateway;
	protected $_cart;
	protected $_useCart;
	protected $_sessionKey = 'Majisti_Shopping_Bill_Session';
	/**
	 * @var Zend_Session_Namespace
	 */
	protected $_session;
	protected $_expirationDelay;
	protected $_creationTime;
	protected $_isLocked;

	public function setPaymentGateway(Majisti_Shopping_Gateway_Interface $gateway)
	{
		$this->_validateGateway($gateway);
		$this->_gateway = $gateway;
	}

	private function _validateGateway($gateway)
	{
		if( $gateway == null ) {
			throw new Majisti_Shopping_Exception("Gateway can't be null");
		}
	}

	private function _validateCheckout()
	{
		if( !$this->hasCheckedOut() ) {
			throw new Majisti_Shopping_Exception('The bill was never checked out');
		}
	}

	/* Set the cart instance... */
	public static function setCart(Majisti_Shopping_Cart_Factory $cart)
	{
		$this->_cart = $cart;
	}

	public function __construct(Majisti_Shopping_Gateway_Interface $gateway, $useCart = true, $expiration_delay = null)
	{
		$this->setPaymentGateway($gateway);
		$this->_useCart = $useCart;
		if ( $useCart ) {
			$this->_cart = Majisti_Shopping_Cart::getInstance();  // get (lock) the instance
		} else {
			$this->_cart = null;   // do not use the cart
		}
		$this->_expirationDelay = $expiration_delay;
		$this->_creationTime = time();
		$this->_isLocked = false;
	}

	public function success()
	{
		$this->_validateGateway($this->_gateway);
		$this->_validateCheckout();

		$this->_gateway->onSuccess(array());
	}

	public function fail()
	{
		$this->__validateCheckout();

		$this->_gateway->onFail(array());
	}

	/**
	 * Default transaction ID generation function.
	 *
	 * @return string Transaction Id
	 */
	protected function _generateTransactionId()
	{
		//TODO: Modify it so it becomes more flexible.
		$crypto = new Majisti_Crypto_Serial(array(
			'sequence_count' 	=> 1,
		));

		return $crypto->encode(array(
			0 => Majisti_User::getInstance()->id
		));
	}

	/**
	 * @return int
	 */
	public function getTransactionId()
	{
		$session = $this->_getSession();
		/* lazy generation */
		if($session->transactionId == null ) {
			$session->transactionId = $this->_generateTransactionId();
		}
		return $session->transactionId;
	}

	/**
	 * Displays infos about the items selected by a user in order for that user
	 * to confirm transaction and proceed to payment gateway.
	 *
	 */
	public function checkout($lock = false)
	{
		$model = array();

		if(!is_null($this->_cart)) {
			foreach ($this->_cart as $item) {
				$model = $item;
			}
		}

		/* FIXME: i18n */
		foreach ($this->getItemsNotInCart() as $item) {
			$model[] = array(
				'key' => 'id',
				'value' => $item->getId(),
				'title' => "Code de l'item:"
			);
			$model[] = array(
				'key' => 'title',
				'value' => $item->getTitle(),
				'title' => "Votre choix:"
			);
			$model[] = array(
				'key' => 'price',
				'value' => $item->getPrice() . "$",
				'title' => "Prix:"
			);
			$model[] = $item->getAttributes() != null ? array(
					'key' => 'attributes',
					'value' => $item->getAttributes(),
					'title' => "Autres informations:"
				)
			: array(
				'key' => 'attributes',
				'value' => 'Aucune',
				'title' => "Autres informations:"
			);
		}

		$this->_getSession()->checkedOut = true;  // bill has checked out
		
		if( $lock ) {
			$this->_getSession()->locked = true;
		}
		
		return $model;
	}

	/**
	 * Function enabling to add items that are not  part of a cart in the bill.
	 * If the boolean flag $_useCart is set to FALSE, the bill will iterate through
	 * the items added with this function instead of iterating in the cart.
	 *
	 * @param mixed items to add to the bill
	 */
	public function addItems($items = array())
	{
		$session = $this->_getSession();
		foreach ($items as $item) {
			$session->items[] = $item;
		}
	}

	public function getItemsNotInCart()
	{
		return $this->_getSession()->items;
	}

	protected function _getSession()
	{
		if( $this->_session === null ) {
			$this->_session = new Zend_Session_Namespace( $this->_sessionKey );
		}
		if ( !isset( $this->_session->useCart ) || !isset( $this->_session->items ) ) {
			$this->_session->transactionId 		= null;
			$this->_session->useCart 			= $this->_useCart;
			$this->_session->items 				= array();
			$this->_session->expirationDelay	= $this->_expirationDelay;
			$this->_session->creationTime 		= $this->_creationTime;
			$this->_session->checkedOut 		= false;
			$this->_session->locked 			= $this->_isLocked;
		}

		return $this->_session;
	}
	
	public function setKey($newKey)
	{
		$this->_sessionKey = $newKey;
	}

	public function clearSession()
	{
		$this->_getSession()->unsetAll();
	}

	public function usesCart($choice)
	{
		$this->_useCart = $choice;
	}

	public function getSubTotal()
	{
		$price = 0;

		if(!is_null($this->_cart) ) {
			foreach ($this->_cart as $item) {
				$price += $item->getPrice();
			}
		} else {
			foreach ($this->getItemsNotInCart() as $item) {
				$price += $item->getPrice();
			}
		}
		return $price;
	}

	/**
	 * Calculates any tax on the bill based on the subTotal passed multiplied by the taxValue factor.
	 *
	 * @param double $subTotal
	 * @param double $taxValue The tax value in PERCENTAGE
	 * @return double tax value
	 */
	public function getTax($subTotal, $taxValue)
	{
		return $subTotal * ($taxValue / 100);
	}

	public function hasCheckedOut()
	{
		return $this->_getSession()->checkedOut;
	}

	public function isTransactionExpired()
	{
		$session = $this->_getSession();

		if( $this->_expirationDelay != null ) {
			$delay = new Zend_Date($this->_expirationDelay);
			return time() > ($session->creationTime + $delay->getTimestamp());
		}
		return false;
	}

	public function getRemainingTime()
	{
		if( !$this->isTransactionExpired() ) {
			return date("H:i:s", ($this->_getSession()->creationTime + $this->_getSession()->expirationDelay) - time());
		} else {
			throw new Majisti_Shopping_Exception('Cannot get remaining time because transaction is expired!');
		}
	}
	
	public function isLocked()
	{
		return $this->_getSession()->locked;
	}
	
	public function lock()
	{
		$this->_getSession()->locked = true;
	}
}