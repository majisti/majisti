<?php
/*
 * Exemple:
 * 
 * The following example must be followed to be able to use variables in an XML file:
 * 
 * <properties>
 * 		<property>
 * 			<name>library</name>
 * 			<value>/project/lib/</value>
 * 		</property>
 * 		... add more property tags for more variables
 * </properties>
 * 
 * In any section afterwards, the variables could then be used like this:
 * 
 * <development>
 * 		<paths>
 * 			<library>${library}</library>
 * 	    	<flash>${library}flash/</flash>
 *		</paths>
 * </development>
 */

/**
 * @desc XML Adapter for Zend_Config. This class has an additionnal responsability which is to
 * recognise specific syntax such as declaring variables (properties) in the XML just
 * like apache ant.
 * 
 * 
 * @author Steven Rosato
 * @version 1.0
 * 	Changelog:
 * 		- First Implementation
 */
class Majisti_Config_Xml extends Zend_Config_Xml
{
	protected $_properties = array();
	
	/**
	 * Class constructor creates a normal Zend_Config_Xml but makes sure that all
	 * variables that could have been used in the XML are replaced by the correct
	 * value.
	 *
     * @param  string  $filename           File to process
     * @param  mixed   $section            Section to process
     * @param  boolean $allowModifications Wether modifiacations are allowed at runtime
     * @throws Zend_Config_Exception When filename is not set
     * @throws Zend_Config_Exception When section $sectionName cannot be found in $filename
	 */
	public function __construct($filename, $section = null,  $allowModifications = false)
	{
		parent::__construct($filename, $section, true);
		
		try {
			$this->_properties = $this->_loadProperties(new Zend_Config_Xml($filename, 'properties', false));
		} catch( Zend_Config_Exception $ignore ) {
			Zend_Debug::dump($ignore, '<strong>: </strong>', false);
		}
		
		if( count($this->_properties) ) {
			$this->_parse($this->_data);
		}
		
		if( !$allowModifications ) {
			$this->setReadOnly();
		}
	}
	
	
	/**
	 * Attempt to use preg_match to find a property in the current array returning
	 * to a maximum 1 found property.
	 *
	 * @param String $pattern The pattern to search with
	 * @param Array $subject The subject used in the preg_match
	 * @return String Will return the found property or an empty string if it couldn't find anything
	 */
	private function _findProperty($pattern, $subject)
	{
		$property = '';
		preg_match($pattern, $subject, $matches);
		if( count($matches) ) {
			$property = $matches[1];
		}
		return $property;
	}
	
	/**
	 * Will return a property's value according to the name given for searching (the key in the key-pair).
	 *
	 * @param String $key The property's name (the key)
	 * @return String | null The property's value if it exists according to the key (property's name)
	 */
	public function getProperty($key)
	{
		if( array_key_exists($key, $this->_properties) ) {
			return $this->_properties[$key];
		} else {
			return null;
		}
	}
	
	/**
	 * @return Array The properties loaded when the constructor was called
	 */
	public function getProperties()
	{
		return $this->_properties;
	}
	
	public function mergeProperties(Majisti_Config_Xml $xml)
	{
		$this->_properties = array_merge($this->getProperties(), $xml->getProperties());
		
		return $this->getProperties();
	}
	
	public function reparse()
	{
		reset($this->_data);
		$this->_parse($this->_data);
		
		return $this;
	}
	
	/**
	 * Fetches all the declared properties in the properties section.
	 *
	 * @param Zend_Config_Xml $properties A Zend_Config loaded only with the properties
	 * @return Array The collected properties
	 */
	protected function _loadProperties(Zend_Config_Xml $properties)
	{
		$collectedProperties = array();
		
		foreach ( $properties->_data as $data) {
			/* One property */
			if( array_key_exists('name', $data->_data) && array_key_exists('data', $data->_data) ) {
				$collectedProperties[$data->_data['name']] = $data->_data['data'];
			} elseif( count($data->_data) ) { /* More than one property */
				foreach ($data->_data as $crumb) {
					$collectedProperties[$crumb->_data['name']] = $crumb->_data['data'];
				}
			}
		}
		
		return $collectedProperties;
	}
	
	/**
	 * @desc Parses recursively the XML file in search of variables. Every variables found
	 * will be replaced with it's associated property value defined in the properties section.
	 *
	 * @param Array $current The current data that contains either Zend_Config_Xml elements or
	 * ready to replace strings.
	 */
	protected function _parse(&$current)
	{
		foreach ($current as $key => $tag) {
			if( $tag instanceof Zend_Config ) {
				$this->_parse($tag->_data);
			} else {
				$replaced = $this->_replace($tag);
				
				if( $replaced != null && $key != NULL ) {
					$current[$key] = $replaced;
				}
			}
		}
	}
	
	/**
	 * Will replace everytying between ${} with the property's value defined at the top of the xml file.
	 *
	 * @param String $tag The tag to replace
	 * @return String The replaced variable with the property's value.
	 */
	private function _replace($tag)
	{
		$pattern = '/\\$\\{(.*)\\}/U';
		
		$foundString = $this->getProperty($this->_findProperty($pattern, $tag));
		
		if( $foundString != null ) {
			return preg_replace($pattern, $foundString, $tag);
		}
		
		return null;
	}
	
//    public function merge(Zend_Config $merge)
//    {
//        foreach($merge as $key => $item) {
//            if(array_key_exists($key, $this->_data)) {
//                if($item instanceof Zend_Config && $this->$key instanceof Zend_Config) {
//					$temp = $this->$key; //Fix
//					if( isset($temp->_data[0]) ) {
//						$item = array($item->toArray());
////						Zend_Debug::dump($item);
////						exit;
////						Zend_Debug::dump($this->$key);exit;
//					} else {
//						$item = $item->toArray();
////						Zend_Debug::dump($item);
//					}
//               		if( isset($temp->_data[0]) ) {
////                    	Zend_Debug::dump($this->$key);
////                    	Zend_Debug::dump($item);
//                    	
////                    	exit; 
//                    }
//                    $this->$key = $this->$key->merge(new Zend_Config($item, !$this->readOnly()));
//                    if( isset($temp->_data[0]) ) {
//                    	Zend_Debug::dump($this->$key);
//                    	exit;
//                    }
//                } else {
//                    $this->$key = $item;
//                }
//            } else {
//                if($item instanceof Zend_Config) {
//                    $this->$key = new Zend_Config($item->toArray(), !$this->readOnly());
//                } else {
//                    $this->$key = $item;
//                }
//            }
//        }
//		Zend_Debug::dump($this->toArray());
//		exit;
//        return $this;
//    }

	
    protected function _arrayMergeRecursive($firstArray, $secondArray)
    {
        if (is_array($firstArray) && is_array($secondArray)) {
            foreach ($secondArray as $key => $value) {
                if (isset($firstArray[$key])) {
                	if( is_array($firstArray[$key]) ) { //Fix
                		if( isset($firstArray[$key][0]) ) {
							$value = array($value); 
                		}
                	}
                    $firstArray[$key] = $this->_arrayMergeRecursive($firstArray[$key], $value);
                } else {
                    $firstArray[$key] = $value;
                }
            }
        } else {
            $firstArray = $secondArray;
        }

        return $firstArray;
    }
}
?>