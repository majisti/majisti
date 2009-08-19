<?php


abstract class Majisti_View_Html_Abstract extends Zend_View_Helper_HtmlElement
{
	
	/**
	 * Returns the HTML string from the given parameters.
	 *
	 * @param string $tag             the HTML tag
	 * @param string $content         the HTML tag's content
	 * @param array $attribs   the HTML tag's attributes
	 */
	protected function _getHtml($tag, $content = null, $attribs = array()) {
		
		if ( empty($content) && $this->_isXhtml() ) {
			$closingBracket = ' /';
		} else {
			$closingBracket = '>'.$content.'</'.$tag;
		}
		
		return '<'.$tag.$this->_htmlAttribs($attribs).$closingBracket.'>';
	}
	
}