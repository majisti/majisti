<?php


class Majisti_View_Helper_HtmlImage extends Majisti_View_Html_Abstract 
{
	
	/**
	 * Create an image element with the given $src and $alt value.
	 * The image element may also be specified with custom attributes
	 * given by $attribs.
	 *
	 * @param string $src
	 * @param string $alt
	 * @param string $attribs (optional)
	 */
	public function htmlImage($src, $alt, $attribs = array()) {
		$attribs = array_merge(array('src' => $src, 'alt' => $alt), $attribs);
		return $this->_getHtml('img', null, $attribs);
	}
	
}