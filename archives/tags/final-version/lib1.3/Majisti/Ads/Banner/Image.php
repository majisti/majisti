<?php

/**
 * The type of the banner is an image. The source of the banner may
 * be an image in the supported formats (ie: gif, jpg, png, etc.)
 * 
 * @author Yanick Rochon
 */
class Majisti_Ads_Banner_Image extends Majisti_Ads_Banner_Abstract 
{
	
	/**
	 * The content of an image banner is it's file on the server.
	 * Set this value to relative path to the file on the server.
	 * 
	 * Ie: <img src="BASE_URL . / . getContent()" />
	 * 
	 * Where the URL is the <a> tag of the image.
	 *
	 * @param string $content
	 */
	public function setContent($content) {
		parent::setContent($content);
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
		), $this->getAttribs()->toArray());
		
		$img_url = $this->getContent();
		if ( 0 !== stripos('http://', $img_url) ) {
			$img_url = APPLICATION_URL . '/' . $img_url;
		}
		
		return $this->getView()->htmlContainer('a', 
			$this->getView()->htmlImage($img_url, $this->getName(), $attribs),
			array('href' => $this->getUrl())
		);
	}
	
}