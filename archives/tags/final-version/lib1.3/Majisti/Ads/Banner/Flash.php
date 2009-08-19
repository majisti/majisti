<?php

/**
 * The type of the banner is a flash animation. The source of the
 * banner may have any valid flash extension (ie: flv, swf, etc.)
 * 
 * @author Yanick Rochon 
 */
class Majisti_Ads_Banner_Flash extends Majisti_Ads_Banner_Abstract 
{
	
	/**
	 * The replacement content if the flash player is not installed
	 * on the machine. This string will be displayed as a gracefully
	 * degrade on fail to load the flash banner.
	 *
	 * @param string $content
	 */
	public function setContent($content) {
		parent::setContent($content);
	}
	
	/**
	 * Override the protected to allow public use of it
	 * @see Majisti_Ads_Banner_Abstract::setParams()
	 *
	 * @param array $params
	 */
	public function setParams(array $params) {
		parent::setParams($params);
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
	public function toString() {
		$attribs = array_merge(array(
			'title' => $this->getName(),
			'width'  => (string) $this->getWidth(),
		  'height' => (string) $this->getHeight()
		), $this->getAttribs()->toArray());
		$params = $this->getParams()->toArray();
		
		return $this->getView()->htmlFlash($this->getUrl(), $attribs, $params, $this->getContent() );
	}
	
	
}