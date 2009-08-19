<?php

/**
 * The type of the banner is a media. The source of the banner should
 * be supported by the browser, and may have any valid extension
 * (ie: wav, mp3, avi, mpgeg, mov, rm, etc.)
 * 
 * @author Yanick Rochon
 */
class Majisti_Ads_Banner_Media extends Majisti_Ads_Banner_Abstract 
{
	
	/**
	 * The banner media type is quicktime
	 *
	 */
	const MEDIA_TYPE_QUICKTIME = 'quicktime';
	
	/**
	 * @see Majisti_Ads_Banner_Abstract 
	 *
	 * @param array $options
	 */	
	public function __construct($options = array()) {

		// TODO : check media type
		
		parent::__construct($options);
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
			'style' => 'width:' . $this->getWidth() . ';'
		           . 'height:' . $this->getHeight() . ';'
		), $this->getAttribs());
		return $this->getView()->htmlFlash($this->getUrl(), $attribs, $this->getParams(), $this->getContent());
	}
	
		
}