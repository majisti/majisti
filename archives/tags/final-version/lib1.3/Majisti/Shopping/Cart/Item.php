<?php 

/**
 * An item to place in the cart. This item object extends
 * ArrayObject
 *
 * @author Yanick Rochon and Jean-Francois Hamelin
 */

class Majisti_Shopping_Cart_Item
{
	/* Cart item variables : Every item MUST have an ID, a title and a price.  Other arguments are optional. */
	protected $_id;
	protected $_price;
	protected $_title;
	protected $_attributes;
	
	public function __get($name)
	{
		switch( $name ) {
			case 'id':
				return $this->getId();
			case 'price':
				return $this->getPrice();
			case 'title':
				return $this->getTitle();
			case 'attributes':
				return $this->getAttributes();
		}
		return null;
	}
	
	/**
	 * Constructor of the cart's items.  Title and price fields are mendatory.
	 *
	 * @param String $title
	 * @param Double $price
	 * @param mixed $args
	 */
	public function __construct($id, $title, $price, $args = array()) 
	{
		$this->_id = $id;
		$this->_title = $title;
		$this->_price = $price;
		$this->_attributes = $args;
	}
	
	/**
	 * Price's accessor
	 *
	 * @return Double price
	 */
	public function getPrice() 
	{
		return $this->_price;
	}
	
	/**
	 * Title's accessor
	 *
	 * @return String title
	 */
	public function getTitle()
	{
		return $this->_title;
	}
	
	/**
	 * Other attributes' accessor
	 *
	 * @return array attributes
	 */
	public function getAttributes()
	{
		return $this->_attributes;
	}
	
/**
	 * Item's ID accessor
	 *
	 * @return String id
	 */
	public function getId()
	{
		return $this->_id;
	}
}