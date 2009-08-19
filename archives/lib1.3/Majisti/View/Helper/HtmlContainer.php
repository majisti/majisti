<?php


class Majisti_View_Helper_HtmlContainer extends Majisti_View_Html_Abstract  
{
	
	/**
	 * Create an HTML container element with the given $tag and $content. 
	 * The container element may also be specified with custom attributes 
	 * given by $attribs. If no content is specified, an empty container
	 * will be returned.
	 *
	 * @param string $tag
	 * @param string $content
	 * @param string $attribs (optional)
	 */
	public function htmlContainer($tag, $content = null, array $attribs = array()) {
		return $this->_getHtml($tag, $content, $attribs);
	}
	
}