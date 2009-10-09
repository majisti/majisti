<?php

/**
 * @desc ArrayObject that has the possibility to
 * return a value based on a key by object access.
 * 
 * e.g:
 * 
 * $this->append('foo');
 * 
 * $this->foo will then work along with $this['foo']
 * 
 * Note that doing:
 * 
 * $this->foo = 'bar';
 * $this['foo'] = 'baz';
 * 
 * calling $this->foo will return 'bar' and not baz.
 * 
 * @author Steven Rosato
 */
class Anato_Util_ArrayObject extends ArrayObject
{
	public function __get($name)
	{
		if( isset($this->{$name}) ) {
			return $this->{$name};
		}
		
		return $this[$name];
	}
}
