<?php

/**
 * 
 * @author Yanick Rochon
 * @version 1.0
 */
require_once 'Zend/View/Interface.php';

/**
 * TextTrim helper
 * 
 * @todo FIXME: not multi-byte safe!!!
 *
 * @uses viewHelper Majisti_View_Helper
 */
class Majisti_View_Helper_TextTrim {
	
	private static $globalDefaultLength = 256;
	private static $globalDefaultPosition = 1.;
	private static $globalDefaultSubstitute = '...';
	
	/**
	 * @var int
	 */
	private $_defaultLength;
	/**
	 * @var float
	 */
	private $_defaultPosition;
	/**
	 * @var string
	 */
	private $_defaultSubstitute;
	
	
	/**
	 * @var Zend_View_Interface 
	 */
	public $view;
	
	/**
	 * @param string $text  the text to trim
	 * @param int $length (Optional)
	 * @param float $position (Optional)
	 * @param string $substitute (Optional)
	 * @return string       the text trimmed
	 */
	private function _trim($text, $length = null, $position = null, $substitute = null)
	{
		if ( is_null( $length ) ) {
			$length = $this->getDefaultLength();
		}
		if ( is_null( $position ) ) {
			$position = $this->getDefaultPosition();	
		}
		if ( is_null( $substitute ) ) {
			$substitute = $this->getDefaultSubstitute();
		}

		if (mb_strlen($text) - mb_strlen($substitute) > $length) {
			$trimLength = (mb_strlen($text) - $length) + mb_strlen($substitute);
			
			$offset = max(floor($position * (mb_strlen($text) - 1)) - floor(($trimLength - 1) * $position), 0);
			if (0 == $offset && 0.5 <= $position) {
				$offset++;			
			}
			
//			echo round($position, 2) 
//			  . ' of "' . $text . '"(' . strlen($text) .')'
//			  . ' offset=' . $offset . ' trimming=' . $trimLength . ' chars'
//			  . ' final size of ' . $length 
//			  . ' = ' . substr($text, 0, $offset) 
//			  . ' [' . substr($text, $offset, $trimLength)
//			  . '] ' . substr($text, $offset + $trimLength) . "<br />\n";

			
			$text = $this->_applyTrim($text, $offset, $trimLength, $substitute);
			
			// TODO : trim to word
			
		}
		
		return $text;
	}
	
	/**
	 * @desc Application function
	 *
	 * @param string $text
	 * @param int $offset
	 * @param string $substitute
	 */
	private function _applyTrim($text, $offset, $trimLength, $substitute) {
		$encoding = $this->view->getEncoding();
		
		return mb_substr($text, 0, $offset, $encoding)
			. $substitute
			. mb_substr($text, $offset + $trimLength, $encoding);
	}
	
	/**
	 * @param string|null $text (optional)
	 * @param int|null $length (optional)
	 * @param float|null $position (optional)
	 * @param string|null $substitute (optional)
	 * @return Majisti_View_Helper_TextTrim|null
	 */
	public function textTrim($text = null, $length = null, $position = null, $substitute = null) {
		if ( is_null( $text ) ) {
			return $this;			
		} else {
			return $this->_trim($text, $length, $position, $substitute);
		}
	}
	
	/**
	 * @return int   the default length that a paragraph should be trimmed to
	 */
	public function getDefaultLength()
	{
		if ( empty( $this->_defaultLength ) ) {
			$this->_defaultLength = self::$globalDefaultLength;
		}
		return $this->_defaultLength;
	}
	
	/**
	 * @return float 
	 */
	public function getDefaultPosition() {
		if ( empty( $this->_defaultPosition ) ) {
			$this->_defaultPosition = self::$globalDefaultPosition;
		}
		return $this->_defaultPosition;
	}
	
	/**
	 * @return string   the default substitute string
	 */
	public function getDefaultSubstitute()
	{
		if ( empty( $this->_defaultLength ) ) {
			$this->_defaultSubstitute = self::$globalDefaultSubstitute;
		}
		return $this->_defaultSubstitute;
	}
	
	/**
	 * @param int $length  the default length that a parapraph should be trimmed to
   	 */
	public function setDefaultLength($length) {
		$this->_defaultLength = $length;
	}
	
	/**
	 * @param float $position set trim position: 0=left, 1=right, 0.5=center 
   	 */
	public function setDefaultPosition($position) {
		$this->_defaultPosition = $position;
	}
	
	/**
	 * @param string $substitute set the default substitute string
   	 */
	public function setDefaultSubstitute($substitute) {
		$this->_defaultSubstitute = $substitute;
	}
	
	/**
	 * Sets the view field 
	 * @param $view Zend_View_Interface
	 */
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
}
