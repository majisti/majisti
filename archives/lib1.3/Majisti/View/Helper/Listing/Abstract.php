<?php

/**
 * This is merely a container to hold a iterable object renderer's options
 *
 * @author Yanick Rochon
 *
 */
abstract class Majisti_View_Helper_Listing_Abstract
{

	/**
	 * The default layout
	 */
	const DEFAULT_LAYOUT = 'partials/listing/default.phtml';
	
	/**
	 * @var string
	 */
	private $_header;
	/**
	 * @var string
	 */
	private $_body;
	/**
	 * @var string
	 */
	private $_footer;
	/**
	 * @var string
	 */
	private $_layout;

	/**
	 * @var stdClass<Iterator>
	 */
	private $_objectIterable;
	
	/**
	 * @var string
	 */
	private $_emptyMessage;

	/**
	 * @var Zend_View_Abstract
	 */
	private $_view;
	
	
	public function __construct($view, $options)
	{
		$this->_view = $view;

		$options = new Majisti_Object($options);
		$this->_layout = $options->layout;
		$this->_header = $options->header;
		$this->_body   = $options->body;
		$this->_footer = $options->footer;
		
		if ( empty($this->_layout) ) {
			$this->_layout = self::DEFAULT_LAYOUT;
		}
		
		$this->_objectIterable = $options->objectIterable;
	}

	
	/**
	 * Return the header script to use when rendering the object iterable.
	 * If the value returned is null, no script will be output for the header
	 *
	 * @return string|null
	 */
	public function getHeader() {
		return $this->_header;
	}
	
	/**
	 * Return the body script to use when rendering the object iterator.
	 * If the value is empty, or is not a valid script, an error will be
	 * thrown when rendering.
	 *
	 * @return string
	 */
	public function getBody() {
		return $this->_body;
	}
	
	/**
	 * Return the message to display when there is no item in the listing.
	 *
	 * @return string
	 */
	public function getEmptyMessage() {
		return $this->_emptyMessage;
	}
	
	/**
	 * Return the footer script to use when rendering the object iterator.
	 * If the value returned is null, no script will be output for the footer.
	 *
	 * @return string|null
	 */
	public function getFooter() {
		return $this->_footer;
	}
	
	/**
	 * Return the layout to use when rendering the object iterable
	 * 
	 * @return string
	 */
	public function getLayout()
	{
		return $this->_layout;
	}
	
	

	/**
	 * @return stdClass<Iterator>
	 */
	public function getObjectIterable()
	{
		return $this->_objectIterable;
	}

	/**
	 *
	 * @return Zend_View_Abstract
	 */
	public function getView()
	{
		return $this->_view;
	}
	
	/**
	 * Function called when the object gets rendered
	 * @see Majisti/View/Helper/Listing/Abstract#render()
	 * 
	 * @param array $options    the array of options that will be sent to the partial
	 */
	abstract function preRender(& $options);

	/**
	 * Render the paginator here
	 *
	 * @return string
	 */
	function render() {
		// set the renderer's pagination utility functions
		
		$options = array();
		$options['objectIterable'] = $this->getObjectIterable();
		if ( !empty($this->_emptyMessage ) ) {
			$options['emptyMessage'] = $this->_emptyMessage;
		}

		$this->preRender($options);

		try {
		
			if ( empty($this->_header) && false !== $this->_header ) {
				$header = ($this->_objectIterable instanceof Zend_Paginator) && count($this->_objectIterable) > 0 
					? (string) $this->_objectIterable
					: null;
			} else {
				$header = $this->_view->partial( $this->_header, $options );
			}
			if ( empty($this->_footer) && false !== $this->_header ) {
				$footer = ($this->_objectIterable instanceof Zend_Paginator) && count($this->_objectIterable) > 0 
					? (string) $this->_objectIterable
					: null;		
			} else {
				$footer = $this->_view->partial( $this->_footer, $options );
			}
			
			$options['body'] = $this->_view->partial( $this->_body, $options );
			// add the header and footer
			$options['header'] = $header;
			$options['footer'] = $footer;
		
			// send to the layout
			$buffer = $this->_view->partial( $this->getLayout(), $options );
		} catch (Exception $e) {
			echo $e;

			$buffer = '';
		}
		
		return $buffer;
	}


	/**
	 * Sets the header script to use for rendering the object iterator.
	 * If the value is null, the default rendering behavior will be applied.
	 * The script will receive all the options in parameter 
	 * (ie: $this->objectIterator will return the objectIterator)
	 * 
	 * @see render()
	 *
	 * @param string $header
	 * @return Majisti_View_Helper_Listing_Abstract
	 */
	public function setHeader($header) {
		$this->_header = $header;
		
		return $this;
	}

	/**
	 * Sets the body script to use for rendering the object iterator.
	 * The script must be valid. The script will receive all the options
	 * in parameter (ie: $this->objectIterator will return the objectIterator)
	 * 
	 * @see render()
	 *
	 * @param string $body
	 * @return Majisti_View_Helper_Listing_Abstract
	 */
	public function setBody($body) {
		if ( empty($body) ) {
			throw new Majisti_View_Helper_Listing_Exception('empty body script');
		}
		
		$this->_body = $body;
		
		return $this;
	}
	
	/**
	 * Sets the message to display when there is no item in the object iterable.
	 * The message may be an HTML formatted string, or simple text. Whatever the
	 * message, it will be wrapped inside a <div> element. The $message argument
	 * may also be an object that implements the __toString() method (or any
	 * object that can be converted to string)
	 *
	 * @param mixed $message
	 */
	public function setEmptyMessage($message) {
		$this->_emptyMessage = $message;
		
		return $this;
	}
	
	/**
	 * Sets the footer script to use for rendering the object iterator.
	 * If the value is null, the default rendering behavior will be applied.
	 * The script will receive all the options in parameter 
	 * (ie: $this->objectIterator will return the objectIterator)
	 *  
	 * @see render()
	 *
	 * @param string $header
	 * @return Majisti_View_Helper_Listing_Abstract
	 */
	public function setFooter($footer) {
		$this->_footer = $footer;
		
		return $this;
	}

	/**
	 * Sets the layout script to use for rendering the object iterator.
	 * The value can not be null and must be a valid script file.
	 *
	 * @throws Majisti_View_Helper_Listing_Exception 
	 * @param string $layout
	 * @return Majisti_View_Helper_Listing_Abstract
	 */
	public function setLayout($layout)
	{
		if ( empty($layout) ) {
			throw new Majisti_View_Helper_Listing_Exception('empty layout');
		}
		
		$this->_partial = $partial;

		return $this;
	}

	/**
	 * Set the object iterable. If the object is an instance of Zend_Paginator,
	 * then the header and footer will automatically be set by the paginator.
	 * 
	 * NOTE : The header and footer may be overriden in the preRender method
	 * 
	 * @param mixed<Iterator> $objectIterable the iterable object
	 * @return Majisti_View_Helper_Listing_Options
	 */
	public function setObjectIterable($objectIterable)
	{
		if ( !is_array($objectIterable) && !($objectIterable instanceof Traversable) ) {
			throw new Majisti_View_Helper_Listing_Exception('object must be iterable');
		}
		
		$this->_objectIterable = $objectIterable;

		return $this;
	}


	public function __toString()
	{
		return $this->render();
	}
}