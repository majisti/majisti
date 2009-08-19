<?php

/**
 * Does the same thing has Zend Url View Helper but appends the $_GET parameters
 * and the end of the url.
 * 
 * @author Steven Rosato
 */
class Majisti_View_Helper_Url extends Zend_View_Helper_Url
{
    /**
     * Generates an url given the name of a route. Also, any values inside the $_GET array
     * will be appended to the end of the url with the normal ?param1=foo&$param2=bar syntax
     * if the passed boolean {$appendQueries} is true.
     *
     * @access public
     *
     * @param  array $urlOptions Options passed to the assemble method of the Route object.
     * @param  mixed $name The name of a Route to use. If null it will use the current Route
     * @param  bool $reset Whether or not to reset the route defaults with those provided
     * @return string Url for the link href attribute.
     */
    public function url(array $urlOptions = array(), $name = null, $reset = false, $encode = true, $appendQueries = false)
    {
        $url = parent::url($urlOptions, $name, $reset, $encode);
        
        /* append the queries if the $_GET has at least an item in it */
        if( $appendQueries && count($_GET) ) {
        	$this->_queries = '?';
        	array_walk($_GET, array(&$this, '_buildQuery'));
        	$url .= $this->_queries;
        }
        
        return $url;
    }
    
    private $_queries;
    
    public function _buildQuery($item, $key)
    {
    	/* if it encounters an array, separate it correctly just like zend form would do (not recursive) */
    	if( is_array($item) ) {
    		foreach ($item as $i) {
    			$this->_queries .= "{$key}[]={$i}";
    			
    			if( $i != end($item) || ($i != end($_GET) && $i == end($item)) ) {
    				$this->_queries .= '&';
    			}
    		}
    	} else { /* append an item normally */
	    	$this->_queries .= "{$key}={$item}";
	
	    	if( $item != end($_GET) ) {
	    		$this->_queries .= '&';
	    	}
    	}
    }
}