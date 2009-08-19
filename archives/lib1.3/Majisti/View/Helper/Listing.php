<?php

/**
 * This view helper enables the creation of multiple pagination controls
 * from different data source in a two four step process :
 *
 * 1. get the data source (should be a SQL select) for the paginator
 * 2. create the paginator (no need to associate it with the view)
 * 3. create a new renderer, specifying the paginator and options
 * 4. call $this->listing('name') in the view (where 'name' is
 *    the name of the paginator renderer set on step 3.
 *
 * Renderer types may be implemented for easy customization by simply
 * extending Majisti_View_Helper_Listing_Abstract and implementing
 * the preRender() function. For convenience, a getView() is provided to set
 * view data, and/or use view helpers.
 *
 * @author Yanick Rochon
 */
class Majisti_View_Helper_Listing extends Zend_View_Helper_Abstract
{

	/**
	 * @var array<Majisti_View_Helper_Listing_Abstract>
	 */
	private $renderers;


	public function __construct()
	{
		$this->renderers = array();
	}


	public function listing($name = NULL)
	{
		if ( !is_null( $name ) ) {
			return $this->get($name);
		}

		return $this;
	}

	/**
	 *
	 * @return Majisti_View_Helper_Listing_Abstract
	 */
	public function get($name = 'default')
	{
		if ( !isset( $this->renderers[$name] ) ) {
			throw new Majisti_Exception('renderer "' . $name . '" does not exist');
		}
		return $this->renderers[$name];
	}

	/**
	 * Set a new renderer by type and name. Types can be one of the available
	 * concrete implementations of Majisti_View_Helper_Listing_Abstract.
	 * For example, for Majisti_View_Helper_Listing_Table, $type = 'Table';
	 * If the type uses a different name space, specify the full class name as $type
	 * and set $customType = TRUE
	 *
	 * @param $type string   the type of the renderer
	 * @param $name string   the name of the renderer
	 * @param $options array the options to send to the type
	 * @param $customType bool is the type a custom class name?
	 * @return Majisti_View_Helper_Listing_Abstract
	 */
	public function set($type, $name = 'default', $options = array(), $customType = FALSE)
	{
		if ( isset( $this->renderers[$name] ) ) {
			require_once 'Majisti/View/Helper/Listing/Exception.php';
			throw new Majisti_View_Helper_Listing_Exception('renderer "' . $name . '" already exists');
		}

		if ( is_object($type) ) {
			$typeObject = $type;
		} else {
			if ( $customType ) {
				$typeClass = $type;
			} else {
				$typeClass = 'Majisti_View_Helper_Listing_' . ucfirst(strtolower($type));
			}

			$typeObject = new $typeClass($this->view, $options);
		}

		// validate instance (works for all instances
		if ( !($typeObject instanceOf Majisti_View_Helper_Listing_Abstract) ) {
			require_once 'Majisti/View/Helper/Listing/Exception.php';
			throw new Majisti_View_Helper_Listing_Exception('invalid renderer type');
		}

		$this->renderers[$name] = $typeObject;

		return $typeObject;
	}

}