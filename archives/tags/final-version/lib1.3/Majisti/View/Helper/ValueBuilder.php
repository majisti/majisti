<?php

/**
 * View helper to build a value given some formatting rules. Particularly
 * useful when implementing an abstract algorithm on custom objects.
 * 
 * See Majisti_View_Helper_Listing for more details
 *
 * @author Yanick Rochon
 */
class Majisti_View_Helper_ValueBuilder extends Zend_View_Helper_Abstract
{

	/**
	 * @desc
	 * Set this value to TRUE if you want valueBuilder to throw an
	 * exception instead of assuming null on errors
	 *
	 * @var bool
	 */
	public $throwExceptions = false;
	
	/**
	 * Persistent last funciton call
	 *
	 * @var mixed
	 */
	private $_lastFunction;
	
	/**
	 * The source history list for the value builder function
	 *
	 * @var array
	 */
	private $_source_history;
	/**
	 * Recursive counter for the valud builder method
	 *
	 * @var int
	 */
	private $_buildingDepth;
	
	/**
	 * @desc
	 * Build a value. The parameter $format may be a string or an array.
	 * When the format is an array, each element will be iterated and, depending
	 * on it's value, will be processed. The last item of the array (or the string,
	 * if $format is a string) indicates the final value returned the the function.
	 * 
	 * Format types (string) :
	 * 
	 * [_n]${name}        will retrieve the {name} attributes of the current source object
	 *                     Ex: $value = $objSrc->{name}
	 *                    If the source object is in fact not an object, $value will be
	 *                    set to null.
	 * 
	 *                    By default, the last source object is used, but it may be possible
	 *                    to process the format on a different source object from the history.
	 *                     Ex: $format = array(':foo', '_0:bar(_value))
	 *                    is algorithmically equivalent to
	 *                         $value = $objSrc->bar($objSrc->foo());
	 * 
	 *                    The value for n depends on the initial source object and the number 
	 *                    of arguments contained in format. The default behavior is that
	 *                    '_0' always points to the initial source object (the object available
	 *                    at the first format string), _1 is the result of the first format
	 *                    string, and _n is the result at the nth format string. If the source
	 *                    object is null, then _0 will return null also. Objects may be pushed
	 *                    into the history list by creating new ones :
	 *                     Ex:  valueBuilder(array('!stdClass', '_value'))
	 *                    will produce an history such as
	 *                     _0 = null, _1 = object of type stdClass
	 *                    Therefore the last example will return the newly created stdclass
	 *                    
	 * 
	 * [_n]:{call}[(...)] will call the function {call} on the current source object
	 *                     Ex: $value = $objSrc->{call}()
	 *                    If {call} is not a function of the source object, $value will be
	 *                    set to null
	 *                    To specify a $objSrc from the source history stack, prepend _{n}
	 *                    at the beginning of the format string. Where {n} is the source
	 *                    index in the history, where 0 is the original callee source object.
	 *                    By default, the last source object is used. If n is greater than
	 *                    the number of source history element, $value is set to null
	 * 
	 *    {call}[(...)]   will call the function {call} but on the global scope
	 *                     Ex: $value = {call}
	 *                    If the function cannot be called, $value will be set to null
	 * 
	 *    !{type}[(...)}  create a new object of type {type}.
	 *                     Ex: !array(=1,=2)     
	 *                    will construct the array array(1,2)
	 *                     Ex: !Majisti_Object(!array($name1, $name2),:callback))
	 *                    will construct a new Majisti_Object with array($objSrc->name1, $objSrc->name2)
	 *                    as parameter with $objSrc->callback as callback function
	 * 
	 *    _lastFunction   will call the last function call using the same namespace and
	 *                    scope as the last function called. This format works if and only
	 *                    if a function has been previously called, if so it is ignored.
	 * 									  The last function DOES NOT apply to the global {call} format.
	 * 
	 *    _value          use the last object processed
	 * 
	 *    _view           return the Zend_View instance assigned to the value builder.
	 *                    To call a view helper: _view:helper(params)
	 *                     Ex: _view:textTrim(_value, =100)
	 *                    Will call the TextTrim view helper passing the last value as
	 *                    first param and the litteral 100 as second param.
	 *  
	 *    ={string}       will set the cell value litterally with the value following the
	 *                    equal ('=') sign. Ex: $value = "{string}"
	 *                    Note that $format = '=' will build an empty string ('')
	 * 
	 *   [_n]+_m          apply an arithmetic operation between two object sources.
	 *   [_n]-_m           Ex: $format = array('=6', '=36', '_1+_2')  // = 42
	 *   [_n]++           The same rules apply as to apply such operation on PHP variables,
	 *   [_n]--           meaning that : "str1" + "str2" = 0 (string additions)
	 *   [_n]*_m          Note that the . operator always treats both parts as string.
	 *   [_n]/_m          The left part (_n) is optional, if not specified, it will be the 
	 *   [_n]%_m          last source object in the source history. If _m is not a valid
	 *   [_n]._m          source history index, an exception will be thrown.
	 * 
	 * 
	 * Format types (other) :
	 * 
	 *   object           An object can be explicitely set as value.
	 *                     Ex: $format = array( new Majisti_Object() ) // = Majisti_Object
	 *                    Likewise, an array for static data can be specified.
	 *                     Ex: $format = array( array('foo' => 1, 'bar' => 2), '$foo' ) // = 1
	 * 
	 *   function         A user function can be fed as value part for more custom control
	 *                    over the data. This function will receive only one paramter; the
	 *                    last source object evaluated.
	 *                     Ex: $format = aray( '=hello', create_function('$s','return $s." world!";') );  
	 *                        // = 'hello world!'
	 *              
	 * Note : To ignore exceptions during the building process, set the $throwException
	 *        class attribute to false (default behavior) or true if you wish to debug.                    
	 *        If it is set to false, every erronous data will be treated as null values.
	 *        In such case, the function may as well return null. Although, a null value
	 *        returned may not be because of erronous data in the format strings. 
	 *        Be aware!
	 * 
	 * NOte : if this format is given : $format = array('=hello', '', '=world', '_1._3')
	 *        One might expect the result to be 'helloworld' when, in fact, will result
	 *        in an exception being thrown (or 'hello' if $throwExceptions is set to false)
	 *        because empty format types are simply ignored, skipped. To avoid such
	 *        mistake, make sure that there are no empty format types. For example :
	 *        $format = array('=hello', '=', '=world', '_1._3') will return 'helloworld'
	 *
	 * @param  string|array $format              the format sequence
	 * @param  mixed        $objSrc              the source object
	 * @param  array        $options OPTIONAL    undocumented
	 * @return mixed
	 */	
	public function valueBuilder($format = null, $objSrc = null, $options = array()) 
	{
		if ( is_null($format) && is_null($objSrc) ) {
			return $this;
		}
		
		$this->_source_history = array($objSrc);
		$this->_buildingDepth = -1; // not yet inside the _valueBuilderImpl method
		
		return $this->_valueBuilderImpl($format, $options);
	}
	
	/**
	 * Internal implementation for the buildValue method.
	 *
	 * @param array|string $format
	 * @param array $options
	 */
	private function _valueBuilderImpl($format, $options) {
		
		$this->_buildingDepth++;
		$matches = null;
		$value = null;
		
		if ( !is_array($format) && !($format instanceof Iterator) ) {
			$format = array($format);
		}
		
		foreach ($format AS $part) {
			if ( $this->isCallable($part) ) {
				$part = call_user_func($part, $this->_getObjectSource());	
			} else if (is_string($part)) {
				$part = ltrim($part);  // no white spaces before
				
				/* ***** Empty ***** */
				if ( empty($part) ) {
					continue;  // ignore this format type and continue
					
				/* ***** Litteral ***** */
				} else if ( '=' === $part[0] ) {
					$part = substr($part, 1);				
					
				/* ***** Object creation ***** */
				} else if ( '!' === $part[0] ) {
					// parse the construct of the object
					list($type, $params) = $this->_parseConstruct(substr($part,1), $options);
					
					switch (strtolower($type)) {
						case 'array':
							$part = $params;
							break;
						default:
							if ( null === ($part = $this->createObject($type, $params)) ) {
								$this->_error('object type cannot be instanciated');
							}
					}
				
				/* ***** Operators ***** */
				} else if ( preg_match('/^([\s\d_]*?)([+\-*\/%.]{1,2})([\s\d_]*)$/', $part, $matches) ) {
					list(,$left,$operator,$right) = $matches;
					
					if ( empty($left) && empty($right) ) {
						$part = $this->_error('malformed artithmetic operation');
					} else {
						if ( empty($left) ) {	
							$left = null;
						}
						if ( empty($right) ) {
							$right = null;
						}
						
						$part = $this->_evalOperation($left, $operator, $right);
					}
					
				/* ***** Object method / attribute ***** */
				} else if ( preg_match('/^(\S*?)([:$])([_a-zA-Z0-9]*)(\((.*?)\))?$/', $part, $matches) ) {
					list(,$prefix,$accessor,$name,$params) = array_pad($matches, 5, null);
	
					// source object
					if ( empty($prefix) ) {
						$prefix = null;
					} //else {
						//echo "Prefix for " . $part . " is " . $prefix . "...";
					//}
					//echo "History count = " . count($this->_source_history) . "\n";
					
					switch ($accessor) {
						case '':     // global call
							$part = $this->_callFunction(null, $name, null, null, true);
							break;
						case ':':    // call
							$part = $this->_callFunction($prefix, $name, $params, $options, false);
							break;
						case '$':    // attribute
							if ( !empty($params) ) {
								$this->_error('attributes cannot have arguments');
							}
							//echo "*** (ATTRIBUTE) part = " . $part . " ...";
							$part = $this->_getProperty($prefix, $name);
							break;
						default:
							$part = $this->_error('unknown accessor ' . $accessor);
					}
					
				/* ***** Constant / backreference ***** */
				} else if ( '_' === $part[0] ) {
					$part = $this->_getObjectSource($part);
					
				/* ***** Global scope functions ***** */
				} else {
					// parse the function construct
					list($fn, $params) = $this->_parseConstruct($part, $options);
					
					if ( $this->isCallable($fn) ) {
						$part = call_user_func_array($fn, $params);
					} else {				
						$part = $this->_error('unknown format type "' . $part . '"');
					}
				}
			}

			// push $part into the history only if not in a recursive mode
			$this->_setObjectSource($part);
		}
		
		if ( !empty($value) ) {
			$value .= $part;
		} else {
			$value = $part;
		}
		
		$this->_buildingDepth--;

		return $value;
	}

	/**
	 * IF the $throwExceptions flag is set to true, then
	 * this function will throw an exception. Otherwise
	 * it will silently return.
	 *
	 * @param string $message
	 * @return null
	 */
	private function _error($message) {
		if ( $this->throwExceptions ) {
			throw new Majisti_View_Exception($message);
		}
		
		return null;
	}
	
	/**
	 * Call a function by name on an object. If the parameter $isGlobal is
	 * set to true, $prefix is ignored, and the function $fName will
	 * be called directly on a global scope.
	 *
	 * @param string $fName
	 * @param int|null $prefix
	 * @param array $params
	 * @param array $options             undocumented
	 * @param bool $isGlobal OPTIONAL
	 * @return mixed
	 */
	private function _callFunction($prefix = null, $fName, $params = array(), $options = array(), $isGlobal = false) {
		// format params
		$params = $this->_parseParams($prefix, substr($params, 1, strlen($params) - 2), $options);
		
		if ( $isGlobal ) {
			if ( function_exists($fName) ) {
				$value = call_user_func_array($fName, $params);	
			} else {
				$vlaue = $this->_error('unknown function ' . $fName);
			}
		} else {
			$fn = array($this->_getObjectSource($prefix), $fName);
			
//			if ( is_null($fn[0]) ) {
//				$class = 'null';
//			} else if ( !is_object($fn[0]) ) {
//				$class = $fn[0];
//			} else {
//				$class = get_class($fn[0]);
//			}
//			echo "History count = " . count($this->_source_history) . ' for ' . $fName . ' on object ' . $class . "\n";
			
			if ( $this->isCallable($fn) ) {
				$this->_lastFunction = array( 'fn' => $fn,	'params' => $params	);
				$value = call_user_func_array($fn, $params);
			} else {
				$value = $this->_error('unknown or inaccessible method : ' . $fName);
			}
		}
		
		return $value;
	}
	
	/**
	 * Evaluates an arthimetic operation.
	 *
	 * @param int|null $left
	 * @param string $operator
	 * @param int|null $right
	 */
	private function _evalOperation($left, $operator, $right) {
		if ( 1 === strlen($operator) ) {
			if ( is_null($right) ) {
				$value = $this->_error('malformed arithmetic operation');
			} else {
				switch ($operator) {
					case '+':
						$value = $this->_getObjectSource($left) + $this->_getObjectSource($right);
						break;
					case '-':
						$value = $this->_getObjectSource($left) - $this->_getObjectSource($right);
						break;
					case '*':
						$value = $this->_getObjectSource($left) * $this->_getObjectSource($right);
						break;
					case '/':
						$value = $this->_getObjectSource($left) / $this->_getObjectSource($right);
						break;
					case '%':
						$value = $this->_getObjectSource($left) % $this->_getObjectSource($right);
						break;
					case '.':
						$value = $this->_getObjectSource($left) . $this->_getObjectSource($right);
						break;
					default:
						$value = $this->_error('unknown operator ' . $operator);
				}
			}
		} else {
			if (!is_null($right)) {
				$this->_error('malformed arithmetic operation');
			}
			$value = $this->_getObjectSource($left);
			
			switch ($operator) {	
				case '++':
					$value++;
					break;
				case '--':
					$value--;
					break;
				default:
					$value = $this->_error('Unknown operator ' . $operator);
			}
		}

		return $value;
	}
	
	/**
	 * Get a property value. The source object specified by $prefix (see 
	 * source history) may be an array, an object or a string (for static 
	 * properties). An error is thrown when the property cannot be read.
	 *
	 * @param int|null $prefix
	 * @param string $name
	 * @return mixed
	 */
	private function _getProperty($prefix, $name) {
		$objSrc = $this->_getObjectSource($prefix);
		
		if ( empty($objSrc) ) {
			$value = $this->_error('cannot get property from a null or empty source object');
		} else if ( is_array($objSrc) ) {
			if ( isset($objSrc[$name]) ) {
				$value = $objSrc[$name];
			} else {
				$value = $this->_error('cannot access array property ' . $name);				
			}
		} else if ( is_string($objSrc) || is_object($objSrc) ) {
			if ( is_string($objSrc) ) {
				$class = new ReflectionClass($objSrc);
			} else if ( is_object($objSrc) ) {
				$class = new ReflectionObject($objSrc);
			}
			if ( $class->hasProperty($name) ) {
				$property = $class->getProperty($name);
				// check accessibility
				if ( (is_object($objSrc) || (is_string($objSrc) && $property->isStatic())) && $property->isPublic() ) {
					$value = $property->getValue($objSrc);
				} else {
					$value = $this->_error('cannot access property ' . $property->getName());
				}
			} else if ( is_object($objSrc) && $class->hasMethod('__get') ) {
				// no property? try to get it through the __get magic method (if it exists)
				$value = $objSrc->__get($name);
			} else {
				$value = $this->_error('unknown property ' . $name);					
			}
		} else {
			$value = $this->_error('source object type cannot have properties');
		}

		return $value;
	}
	
	/**
	 * Utility function to return the value from the source history. If the
	 * value points to an offset out of bound, or is not numeric, an exception 
	 * will be thrown. If $index is not specified, the function returns the last
	 * source object set into the source history
	 *
	 * @param int|string $index OPTIONAL
	 * @return mixed
	 */
	private function _getObjectSource($index = null) {
		if ( is_null($index) ) {
			// get last index by default
			$objSrc = end($this->_source_history);
		} else {
			$index = ltrim($index, '_');
			
			if ( !is_numeric($index) ) {
				switch (strtolower($index)) {
					case 'lastfunction':
						if ( !empty($this->_lastFunction) ) {
							$objSrc = call_user_func_array($this->_lastFunction['fn'], $this->_lastFunction['params']);
						} else {
							$objSrc = $this->_error('_lastFunction is empty');
						}
						break;
					case 'value':
						$objSrc = $this->_getObjectSource();
						break;
					case 'view':
						$objSrc = $this->view;
						break;
					default:
						$objSrc = $this->_error('invalid source history index : ' . $index);
						break;
				}
			} else if ( $index < 0 || $index > count($this->_source_history) ) {
				$objSrc = $this->_error('index out of range for source history : ' . $index);
			} else {
				$objSrc = $this->_source_history[$index];
			}
		}
		
		return $objSrc;
	}
	
	/**
	 * Set an object into the source history
	 *
	 * @param mixed $obj
	 */
	private function _setObjectSource($obj) {
		if ( 0 >= $this->_buildingDepth ) {
			array_push($this->_source_history, $obj);
		}
	}
		
	
	/**
	 * Parase $var and return the type and parameters parts as an array
	 * The function returns array(null, null) if $var is not a valid
	 * construct. The format of $var should be
	 * 
	 *    {name}[({param1}[,{param2}[,...]]]
	 *
	 * @param int|null $prefix
	 * @param string $var
	 * @param array $options
	 * @return array
	 */
	private function _parseConstruct($var, $options) {
		if ( empty($var) || !is_string($var) ) {
			$type = $params = null;
		} else {
			$paramStart = strpos($var, '(');
			if (false === $paramStart) {
				$type = $var;
				$params = array();
			} else {
				$type = substr($var, 0, $paramStart);
				// format params
				$paramLength = strlen($var) - $paramStart - 2;
				if ( 0 < $paramLength ) {
					// NOTE : default $prefix to last source object
					$params = $this->_parseParams(null, substr($var, $paramStart + 1, $paramLength), $options);
				} else {
					$params = array();
				}
			}
		}
		return array($type, $params);
	}
	
	/**
	 * Returns an array with the params built using the buildValue method. The
	 * parameters should be passed as an array, but may also be a string. An
	 * empty string will return array(). And ampty array will return array().
	 * Every empty values within the array will return that value untouched.
	 * 
	 * @param int|null $prefix
	 * @param string|array $params
	 * @param array $options
	 * @return array
	 */
	private function _parseParams($prefix, $params, $options) {
		if ( empty($params) ) {
			$params = array();
		} else {
			$objSrc = $this->_getObjectSource($prefix);
			
			if ( !is_array($params) ) {
				$paramStr = $params;
				$depth = 0;
				$len = strlen($paramStr);
				$params = array();
				for ($offset=0, $lastOffset=0, $subLength=0; $offset<$len; $offset++, $subLength++) {
					switch ($paramStr[$offset]) {
						case '(':
							$depth++;
							break;
						case ')':
							$depth--;
							if ( $depth < 0 ) {
								$this->_error('malformed parameters');
								$depth = 0;
							}
							break;
						case ',':
							if ( 0 === $depth ) {
								$param = substr($paramStr, $lastOffset, $subLength);
								$params[] = $this->_valueBuilderImpl($param, $objSrc, $options); 
								$lastOffset = $offset + 1;
								$subLength = -1;  // next increment will set it to 0
							}
							break;
						default:
					}	
				}
				if ( $depth > 0 ) {
					$this->_error('malformed parameters');
				}
				if ( $subLength > 0 ) {
					$param = substr($paramStr, $lastOffset, $subLength);
					$params[] = $this->_valueBuilderImpl($param, $objSrc, $options); 
				}
			} else {
				foreach ($params as $index => $param) {
					if ( !empty($param) ) {
						$params[$index] = $this->_valueBuilderImpl($param, $objSrc, $options);
					}
				}
			}
		}
		 
		return $params;
	}
	
	/**
	 * Create an new object of type $type and return it. If the object
	 * cannot be created, for any reason, the method returns null.
	 * 
	 * TODO : see if this method should go somewhere else...
	 *
	 * @param string $type
	 * @param array $params
	 * @return mixed
	 */
	public function createObject($type, $params) {
		$obj = null;
		
		// ignore class errors
		if ( @class_exists($type, true) ) {
			$class = new ReflectionClass($type);
			if ( $class->isInstantiable() ) {
				// get constructor
				if ( null === $class->getConstructor() ) {
					// ignore params
					$obj = $class->newInstance();								
				} else {
					$obj = $class->newInstanceArgs($params);
				}
			}
		}
		return $obj;
	}
	
	/**
	 * Check if a function can be called. This is a replacement to both
	 * the PHP functions is_callable() and method_exists() as this method
	 * checks for the visibility and static nature of the function. It
	 * may also be a replacement of function_exists() as it also supports
	 * global functions.
	 * 
	 * The $var parameter may be a string, or an array compatible with
	 * the mentioned functions. 
	 * 
	 * TODO : see if this method should go somewhere else...
	 *
	 * @param mixed $var
	 * @return bool         true if the function exists and can be called, false otherwise
	 */
	public function isCallable($var) {
		$callable = false;
		
		if (is_array($var) && count($var) == 2) {
			$var = array_values($var);
			if ( ((is_string($var[0]) && class_exists($var[0], true)) || is_object($var[0])) ) {
				$isObj = is_object($var[0]);
				$class = new ReflectionClass($isObj ? get_class($var[0]) : $var[0]);
				if ($class->hasMethod($var[1])) {
					$method = $class->getMethod($var[1]);
					if ( $method->isPublic()
						&& (($isObj && !$class->isAbstract()) || (!$isObj && $method->isStatic())) ) {
							
						$callable = true;
					} 
				} elseif ($class->hasMethod('__call')) {  // try the magic method
					$callable = true;  // let it handle
				} 
			}
		} elseif (is_string($var) && function_exists($var)) {
    		$callable = true;
    	} else {
    		$callable = is_callable( $var );
    	}
		
		return $callable;
	}
	
}