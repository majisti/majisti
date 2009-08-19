<?php

/**
 * @desc
 * Provides an easy access to information on existing image. The class
 * supports jpg, gif, png, swf
 * 
 * TODO : add list of supported types
 * TODO : create new images, draw text, lines, etc. (with GD extension)
 * 
 * TODO : needs to be refactored...
 * 
 * @author Yanick Rochon
 */
class Majisti_Image 
{
	
	const TYPE_IMAGE_JPEG  = 2;
	const TYPE_FLASH_SWF   = 13;
	
	
	/**
	 * Default mime type for unknown files
	 *
	 * @var string
	 */
	const MIME_UNKNOWN = '';
	
	/**
	 * The width of the image
	 *
	 * @var int
	 */
	private $_width;
	/**
	 * The height of the image
	 *
	 * @var int
	 */
	private $_height;
	/**
	 * The type of the image
	 *
	 * @var unknown_type
	 */
	private $_type;
	
	/**
	 * The bits (depth) of the image
	 *
	 * @var int
	 */
	private $_bits;
	
	/**
	 * The number of channels
	 *
	 * @var int
	 */
	private $_channels;
	
	/**
	 * The image mimetype
	 *
	 * @var string
	 */
	private $_mimeType;
	
	/**
	 * The filename of the image (may be null)
	 *
	 * @var string
	 */
	private $_filename;
	/**
	 * The path info of the filename
	 *
	 * @var array
	 */
	private $_pathInfo;
	
	
	/**
	 * Create a new image object. If the $filename is specified, and
	 * points to a valid file, the file will be used. If no filename
	 * is provided, a new image will be created. If $filename is not
	 * a valid file, an exception will be thrown
	 */
	public function __construct($filename = null) 
	{
		if ( !empty($filename ) ) {
			if ( !file_exists($filename) ) {
				throw new Majisti_Image_Exception('the specified file does not exist');
			}

			// TODO : read the markers with the second parameter
			$imgData = @getimagesize($filename);
			
			if ( !$imgData ) {
				throw new Majisti_Image_Exception('not an image file');
			}

			list(
				$this->_width, 
				$this->_height, 
				$this->_type) = $imgData;
			
			$this->_bits = isset($imgData['bits']) ? $imgData['bits'] : null;
			$this->_channels = isset($imgData['channels']) ? $imgData['channels'] : null;
			$this->_mimeType = isset($imgData['mime']) ? $imgData['mime'] : self::MIME_UNKNWON;
			
			$this->_filename = $filename;
				
		} else {
			
			throw new Majisti_Image_Exception('not implemented');
			
		}
	}
	
	/**
	 * Return the number of bits (color depth) the image contains
	 *
	 * @return int
	 */
	public function getBits() {
		return $this->_bits;
	}
	
	/**
	 * Returns the filename as specified in the constructor
	 *
	 * @return string
	 */
	public function getFileName() {
		return $this->_filename;
	}
	
	/**
	 * Return the height of the image
	 *
	 * @return int
	 */
	public function getHeight() {
		return $this->_height;
	}
	
	/**
	 * Get the mime/type of the image
	 *
	 * @return string
	 */
	public function getMimeType() {
		return $this->_mimeType;
	}
	
	/**
	 * Get the type of the image
	 * 
	 * TODO: create constants for this
	 *
	 * @return int
	 */
	public function getType() {
		return $this->_type;
	}
	
	/**
	 * Return the width of the image
	 *
	 * @return int
	 */
	public function getWidth() {
		return $this->_width;
	}
	
	
	
}