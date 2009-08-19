<?php

/**
 * The type of the banner is a text value. The source of the banner
 * is the displayed text itself in HTML format. The given format
 * must have been previously validated.
 * 
 * Note : for security reasons, this type of banner should be preset
 *        and should not be specified by an external source.
 * 
 * @author Yanick Rochon
 */
class Majisti_Ads_Banner_Text extends Majisti_Ads_Banner_Abstract 
{

	/**
	 * Sets the content that will be displayed if the flash player
	 * is not installed on the client's browser. The $content value
	 * may be any HTML valid string. 
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
//		$attribs = array_merge(array(
//		  'href'  => $this->getUrl(), 
//			'title' => $this->getName(),
//			'style' => 'width:' . $this->getWidth() . ';'
//		           . 'height:' . $this->getHeight() . ';'
//		), $this->getAttribs()->toArray());
		
//		return $this->getView()->htmlContainer('a', $this->getContent(), $attribs);
		return $this->getContent();
	}
		
}