<?php


class Majisti_View_Helper_HtmlMedia extends Majisti_View_Html_Abstract 
{
	/**
	 * The content type is a quicktime or compatible video file. 
	 *
	 * @var string
	 */
	const MEDIA_TYPE_QUICKTIME = 'quicktime';
	/**
	 * The content type is any other valid media type.
	 * 
	 * @var string
	 */
	const MEDIA_TYPE_OTHER = 'other';
	
	/**
	 * Declares an array of filetypes for autodetection
	 *
	 * @var array
	 */
	private $_filetypes;
	
	/**
	 * Construct and initialize the data
	 */
	public function __construct() {
		$this->_filetypes = array(
			self::MEDIA_TYPE_QUICKTIME => array('mov')
		);
	}
	
	/**
	 * Autodetect the media type based on it's extension
	 *
	 * @param string $data    the file name
	 * @return string
	 */
	private function _autodetectDataType($data) {
		$type = self::MEDIA_TYPE_OTHER;
		
		$ext = pathinfo($data, PATHINFO_EXTENSION);
		
		foreach ($this->_filetypes as $type => $extList) {
			if (in_array($ext, $extList)) {
				$type = $type;
				break;
			}
		}
		
		return $type;
	}
	
	
	/**
	 * Create an HTML media container element with the given $tag and $type. 
	 * The container element may also be specified with custom attributes 
	 * given by $attribs. If no content is specified, an empty container
	 * will be returned. The $data is the source of the media to play. The
	 * data may be autodetected
	 * 
	 * The params may have the following :
	 *
	 * TODO : document this 
	 *                      
	 * 
	 *
	 * @param string $data
	 * @param string $type 
	 * @param array $attribs (optional)
	 * @param array $params (optional)
	 * @param string $content (optional)
	 */
	public function htmlMedia($data, $type, array $attribs = array(), array $params = array(), $content = null) {

		if ( $type == self::MEDIA_TYPE_AUTODETECT ) {
			$type = $this->_autodetectDataType($data);
		}
		
		switch ($type) {
			case self::MEDIA_TYPE_QUICKTIME:
				return $this->view->htmlQuicktime($data, $attribs, $params, $content);				
				break;
			default:
				throw new Majisti_View_Exception('Not implemented');
		}
	}

	
}