<?php


/**
 * This class represents the abstraction of an Ad banner. It's
 * implementation allows to control it's size, weight, and banner's url.
 * 
 * The banner type, output string and source should be specified in
 * concrete implementations of this class.
 * 
 * @author Yanick Rochon
 */
abstract class Majisti_Ads_Banner_Abstract
{

	/**
	 * @var mixed
	 */
	private $_id;
	/**
	 * @var string
	 */
	private $_name;
	/**
	 * @var int
	 */
	private $_width;
	/**
	 * @var int
	 */
	private $_height;
	/**
	 * @var float
	 */
	private $_weight;
	/**
	 * @var string
	 */
	private $_url;
	/**
	 * @var Majisti_Object
	 */
	private $_attribs;
	/**
	 * @var Majisti_Object
	 */
	private $_params;
	/**
	 * @var sstring
	 */
	private $_content;
	/**
	 * @var Zend_View_Abstract
	 */
	private $_view;
	/**
	 * @var bool
	 */
	private $_changed;
	
	/**
	 * Create a new Ad banner. The options may include any or all of the
	 * following :
	 * 
	 *   'id'         mixed        the id banner's id
	 *   'name'       string       the name of the banner 
	 *   'width'      int          the width of the banner in pixels
	 *   'height'     int          the height of the banner in pixels
	 *   'weight'     float        the weight of the banner (see setWeight())
	 *   'url'        string       the bnner's url
	 *   'attribs'    array        extra attributes for the banner element
	 *   'params'     array        some extra parameters (depends on implementation)
	 *   'content'    string       the banner text content (depends on implementation)
	 *
	 * @param array $options
	 */
	public function __construct($options = array()) {
		
		$options = new Majisti_Object($options);
		
		$this->_id = $options->id;
		$this->_name = (string) $options->name;
		$this->_width = (int) $options->width;
		$this->_height = (int) $options->height;
		$this->_weight = (float) $options->weight;
		$this->_url = (string) $options->url;
		$this->_attribs = $options->attribs;
		$this->_params = $options->params;
		$this->_content = (string) $options->content;
		$this->_changed = false;

		if ( !is_numeric($this->_name) && empty($this->_name) ) {
			throw new Majisti_Ads_Banner_Exception('empty name');
		}
		if ( $this->_weight <= 0 ) {
			throw new Majisti_Ads_Banner_Exception('weight must be greater than 0');
		}
		if ( $this->_width < 0 || $this->_height < 0 ) {
			throw new Majisti_Ads_Banner_Exception('negative dimension');
		}
		if ( null !== $this->_attribs && !($this->_attribs instanceof Majisti_Object) ) {
			throw new Majisti_Ads_Banner_Exception('invalid type : attribs');			
		}
		if ( null !== $this->_params && !($this->_params instanceof Majisti_Object) ) {
			throw new Majisti_Ads_Banner_Exception('invalid type : params');
		}
	}
	
	
	/**
	 * See toString()
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->toString();
	}
	
	/**
	 * Return the type of the ad banner based on it's class name
	 *
	 * @return string
	 */
	public function getType() {
		
		$class = get_class($this);
		$typeOffset = strrpos($class,'_');
		if ( false === $typeOffset ) {
			return strtolower($class);
		} else {
			return strtolower(substr($class, $typeOffset + 1));
		}
		
	}
	
	/**
	 * Returns if the banner has been changed since it's creation
	 * (see Majisti_Ads_Backend_Abstract)
	 *
	 * @return bool
	 */
	public function hasChanged() {
		return $this->_changed;
	}
	
	/**
	 * Tells the adapater that the banner has changed since it's
	 * creation
	 * @param bool $flag
	 */
	public function setChanged($flag = true) {
		$this->_changed = $flag;
	}
	
	/**
	 * @desc 
	 * Returns the attributes for the banner element. This function
	 * may return null or an (empty) Majisti_Object depending on the implementation.
	 * See setAttribs() for more details
	 * 
	 * @return Majisti_Object|null
	 */
	public function getAttribs() {
		if ( null === $this->_attribs ) {
			$this->_attribs = new Majisti_Object();
		}
		return $this->_attribs;
	}	
	
	/**
	 * Returns the content for the banner. The content purpose may
	 * vary depending on the implementation. The $content value may
	 * be any HTML valid string or null (will be converted to an
	 * empty string.
	 *
	 * @return string
	 */
	public function getContent() {
		return $this->_content;
	}
	
	/**
	 * Return the banner ad height
	 *
	 * @return int
	 */
	public function getHeight() {
		return $this->_height;
	}
	
	/**
	 * Return the banner's id. The value 
	 *
	 * @return mixed
	 */
	public function getId() {
		return $this->_id;
	}
	
	/**
	 * Return the name of the banner ad.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}
	
	/**
	 * Returns the parameters of the flash banner. The function
	 * may return null or an (empty) Majisti_Object depending on the
	 * implementation.
	 * @see setParams() for more details.
	 *
	 * @return Majisti_Object|null
	 */
	public function getParams() {
		if ( null === $this->_params ) {
			$this->_params = new Majisti_Object();
		}
		return $this->_params;
	}
	
	/**
	 * Returns the banner's url
	 *
	 * @return string
	 */
	public function getUrl() {
		if ( 0 !== stripos($this->_url, 'http://' ) ) {
			return APPLICATION_URL . '/' . $this->_url;
		} else {
			return $this->_url;
		}
	}
	
	/**
	 * Return the weight of the banner. The weight determines how often
	 * a banner should be used compared to another. The value may be
	 * any numeric value
	 *
	 * @return float
	 */
	public function getWeight() {
		return $this->_weight;
	}
	
	/**
	 * Return the banner ad width
	 *
	 * @return int
	 */
	public function getWidth() {
		return $this->_width;
	}
	
	/**
	 * Returns the view for this object. If no view is defined, the function
	 * will attempt to search in the registry ('Majisti_View', 'Zend_View') and
	 * if none found, it will create a new one and return it.
	 *
	 * @return Zend_View
	 */
	public function getView() {
		if ( empty($this->_view) ) {
			$this->_view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		}
		
		return $this->_view;
	}
	
	/**
	 * Sets the banner's container attributes. Note that
	 * the width and height attribute will be removed if specified. 
	 *
	 * @param array|Majisti_Object $attribs
	 */
	public function setAttribs(array $attribs) {
		if ( !is_null($attribs) ) {
			unset($attribs['width'], $attribs['height']);
		}
		
		$this->_attribs = new Majisti_Object($attribs, array($this, 'setAttribs'));
		$this->setChanged();
		
		return $this;
	}
	
	/**
	 * Sets the content that will be displayed if the flash player
	 * is not installed on the client's browser. The $content value
	 * may be any HTML valid string. Implementations should
	 * override this method and make it public :
	 * 
	 *	 public function setParams(array $params) {
	 * 		 parent::setParams($params);
	 *	 }
	 *
	 * @param string $content
	 */
	protected function setContent($content) {
		$this->_content = (string) $content;
		$this->setChanged();
		
		return $this;
	}
	
	/**
	 * Sets the height of the banner.
	 *
	 * @param int $height
	 */
	public function setHeight($height) {
		$this->_height = (int) $height;
		$this->setChanged();
		
		return $this;
	}
	
	/**
	 * Sets the name of the banner ad
	 *
	 * @param string $name
	 */
	public function setName($name) {
		$this->_name = (string) $name;
		$this->setChanged();
		
		return $this;
	}
	
	/**
	 * Sets the banner's url
	 *
	 * @param string $url
	 */
	public function setUrl($url) {
		$this->_url = (string) $url;
		$this->setChanged();
		
		return $this;
	}
	
	/**
	 * Sets the parameters of the banner. The params will be used
	 * to output <params> tags inside the banner's container. Implementations 
	 * should override this method and make it public :
	 * 
	 *	 public function setParams(array $params) {
	 * 		 parent::setParams($params);
	 *	 }
	 *
	 * @param array $params
	 */
	protected function setParams($params) {
		$this->_params = new Majisti_Object($params);
		$this->setChanged();
		
		return $this;
	}
	
	/**
	 * Sets the weight of the banner.The weight determines how often
	 * a banner should be used compared to another. The value may be
	 * any numeric value
	 *
	 * @param float $weight
	 */
	public function setWeight($weight) {
		$this->_weight = (float) $weight;
		$this->setChanged();
		
		return $this;
	}
	
	/**
	 * Sets the width of the banner 
	 *
	 * @param int $width
	 */
	public function setWidth($width) {
		$this->_width = (int) $width;
		$this->setChanged();
		
		return $this;
	}
	
	/**
	 * Sets the view for this object.
	 *
	 * @param Zend_View $view
	 */
	public function setView($view) {
		if ( !($view instanceof Zend_View) ) {
			throw new Majisti_Ads_Banner_Exception('invalid view');
		}
		
		$this->_view = $view;
	}
	
	
	/**
	 * Returns a string representation of the banner. The concrete
	 * implementation should provide the necessary HTML string
	 * to output the current banner. This function should be called
	 * from a view script.
	 * 
	 * Note : use $this->getView() with a HTML element view helper.
	 * 
	 * @return string
	 */
	abstract public function toString();
	
}