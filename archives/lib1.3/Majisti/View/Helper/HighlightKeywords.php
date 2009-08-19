<?php

/**
 * TODO: doc
 * FIXME: some words get broken
 * 
 * @author Steven Rosato
 */
class Majisti_View_Helper_HighlightKeywords extends Zend_View_Helper_Abstract
{
	public function highlightKeywords($text, $keywords, $class = 'bold')
	{
		$keywords = explode(' ', $keywords);

		foreach( $keywords as $keyword ) {
			if (2 < strlen($keyword)) {
				$text = preg_replace("/{$keyword}*\\w+/i", "<span class=\"{$class}\">\\0</span>", $text);
			}
		}
		
		return $text;
	}
}