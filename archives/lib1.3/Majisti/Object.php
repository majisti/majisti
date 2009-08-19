<?php

/**
 * General purpose dynamic object. Instances of this class may be used
 * in foreach loops
 * 
 * @author Yanick Rochon
 */
class Majisti_Object implements Countable, Iterator
{

    /**
     * Iteration index
     *
     * @var integer
     */
    protected $_index;

    /**
     * Number of elements in data
     *
     * @var integer
     */
    protected $_count;

    /**
     * @var array
     */
    protected $_data;
    
    /**
     * @var array
     */
    private $_callbacks;
    

    /**
     *
     * @param mixed          $obj       OPTIONAL
     * @param array|callable $callbacks OPTIONAL
     */
    public function __construct( $obj = null, $callbacks = null )
    {
        $this->_index = 0;
        $this->_data = array();
        if ($obj instanceof Majisti_Object) {
        	$this->_data = $obj->_data;
        } else if (is_array($obj) || ($obj instanceof Iterator)) {
            foreach ($obj as $key => $value) {
                if (is_array($value) || ($value instanceof Iterator)) {
                    $this->_data[$key] = new self($value);
                } else {
                    $this->_data[$key] = $value;
                }
            }
        } else if ( !is_null($obj) ) {
            $this->_data[] = $obj;
        }

        $this->_count = count($this->_data);
        
        if ( !empty($callbacks) ) {
        	if ((!is_array($callbacks) && !($callbacks instanceof Iterator)) || is_callable($callbacks) ) {
        		$callbacks = array($callbacks);
        	}
        	
	        foreach ($this->_callbacks as $callback) {
	        	if ( !is_readable($callback) ) {
	        		throw new Majisti_Exception('not a valid callback');
	        	}
	        }
        } else {
        	$callbacks = array();
        }
        $this->_callbacks = $callbacks;
    }
    
    /**
     * Call every callbacks with $this as argument
     */
    private function _notifyCallbacks() {
    	foreach ($this->_callbacks as $callback) {
    		call_user_func($callback, $this);
    	}
    }

    /**
     * Retrieve a value and return $default if there is no element set.
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get( $name, $default = null )
    {
        $result = $default;
        if (array_key_exists($name, $this->_data)) {
            $result = $this->_data[$name];
        }
        return $result;
    }

    /**
     * Magic function so that $obj->value will work.
     *
     * @param string $name
     * @return mixed
     */
    public function __get( $name )
    {
        return $this->get($name);
    }

    /**
     * Only allow setting of a property if $allowModifications
     * was set to true on construction. Otherwise, throw an exception.
     *
     * @param  string $name
     * @param  mixed  $value
     * @throws Zend_Config_Exception
     * @return void
     */
    public function __set( $name, $value )
    {
        if (is_array($value)) {
            $this->_data[$name] = new self($value);
        } else {
            $this->_data[$name] = $value;
        }
        $this->_count = count($this->_data);
        
        $this->_notifyCallbacks();
    }

    /**
     * Deep clone of this instance to ensure that nested Zend_Configs
     * are also cloned.
     *
     * @return void
     */
    public function __clone()
    {
        $array = array();
        foreach ($this->_data as $key => $value) {
            if ($value instanceof Majisti_Object) {
                $array[$key] = clone $value;
            } else {
                $array[$key] = $value;
            }
        }
        $this->_data = $array;
    }

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function toArray()
    {
        $array = array();
        foreach ($this->_data as $key => $value) {
            if ($value instanceof Majisti_Object) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }
        return $array;
    }

    /**
     * Support isset() overloading on PHP 5.1
     *
     * @param string $name
     * @return boolean
     */
    public function __isset( $name )
    {
        return isset($this->_data[$name]);
    }

    /**
     * Support unset() overloading on PHP 5.1
     *
     * @param  string $name
     * @throws Zend_Config_Exception
     * @return void
     */
    public function __unset( $name )
    {
        unset($this->_data[$name]);
        $this->_count = count($this->_data);
        
        $this->_notifyCallbacks();
    }

    /**
     * Defined by Countable interface
     *
     * @return int
     */
    public function count()
    {
        return $this->_count;
    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->_data);
    }

    /**
     * Defined by Iterator interface
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->_data);
    }

    /**
     * Defined by Iterator interface
     *
     */
    public function next()
    {
        next($this->_data);
        $this->_index ++;
    }

    /**
     * Defined by Iterator interface
     *
     */
    public function rewind()
    {
        reset($this->_data);
        $this->_index = 0;
    }

    /**
     * Defined by Iterator interface
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->_index < $this->_count;
    }

    /**
     * Merge another Zend_Config with this one. The items
     * in $merge will override the same named items in
     * the current config.
     *
     * @param Zend_Config $merge
     * @return Zend_Config
     */
    public function merge( Majisti_Object $merge )
    {
        foreach ($merge as $key => $item) {
            if (array_key_exists($key, $this->_data)) {
                if ($item instanceof Majisti_Object && $this->$key instanceof Majisti_Object) {
                    $this->$key = $this->$key->merge(new Majisti_Object($item->toArray()));
                } else {
                    $this->$key = $item;
                }
            } else {
                if ($item instanceof Majisti_Object) {
                    $this->$key = new Majisti_Object($item->toArray());
                } else {
                    $this->$key = $item;
                }
            }
        }
        
        $this->_notifyCallbacks();
        
        return $this;
    }
}
