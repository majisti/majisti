<?php

/*
 * Test helper
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . str_repeat('..'.DIRECTORY_SEPARATOR, 3) . 'TestHelper.php';

/**
 * Test the Majisti_Ads class
 *
 * @lastmodified 2009-03-07
 *
 * @author Yanick Rochon
 * @version 1
 */
class Majisti_View_Helper_ValueBuildTest extends Majisti_Test_PHPUnit_TestCase
{

	/**
	 * @var Majisti_View_Helper_ValueBuilder
	 */
	private $mock;
	/**
	 * @var Majisti_View_Helper_ValueBuilderMockSource
	 */
	private $source;
	/**
	 * The original error handler (bypass to skip errors)
	 *
	 * @var mixed
	 */
	private $lastErrorHandler;

	public function setUp() {
		$this->mock = new Majisti_View_Helper_ValueBuilder();
		$this->mock->throwExceptions = true;
		
		$this->mock->setView(new Majisti_View());
		
		$this->source = new Majisti_View_Helper_ValueBuilderMockSource();
	}

	public function testValueBuilderValue() {
		// single (null)
		$format = '_value';
		$value = $this->mock->valueBuilder($format, null);
		$this->assertEquals(null, $value);
		
		// single (primitive:int)
		$format = '_value';
		$value = $this->mock->valueBuilder($format, 123);
		$this->assertEquals(123, $value);
		
		// single (primitive:float)
		$format = '_value';
		$value = $this->mock->valueBuilder($format, 123.456);
		$this->assertEquals(123.456, $value);
		
		// single (primitive:string)
		$format = '_value';
		$value = $this->mock->valueBuilder($format, "123");
		$this->assertEquals("123", $value);
		
		// single (object)
		$format = '_value';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals($this->source, $value);

		// with leading space (should safely be trimmed)
		$format = ' _value';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals($this->source, $value);

		// case insensitive
		$format = ' _VaLUe';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals($this->source, $value);
		
		// multiple
		$format = array_fill(0, 5, '_value');
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals($this->source, $value);
	}
	
	public function testValueBuilderLitteral() {
		// literal string
		$format = '=1';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals(1, $value);
		
		// literal array
		$format = array('=1', '=2', '=3');
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals(3, $value);  // last value returned
		
		// empty litteral
		$format = '=';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals('', $value);  // last value returned
		
		// equal sign '='
		$format = '==';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals('=', $value);  // last value returned
		
	}
	
	public function testValueBuilderAttrib() {
		// attribute
		$format = '$_foo';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals($this->source->_foo, $value);

		$format = '$_bar';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals($this->source->_bar, $value);

		try {
			
			// private attribute
			$format = '$_priv';
			$value = $this->mock->valueBuilder($format, $this->source);
			$this->fail('access private attributes should throw an exception');
			
		} catch (Exception $e) {
			// ok
		}
		
	}
	
	public function testValueBuilderCall() {
		// function name only
		$format = ':foo';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals($this->source->foo(), $value);

		// function no param
		$format = ':bar()';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals($this->source->bar(), $value);
				
		// function with function param (no arg)
		$format = ':bleh(:foo)';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals($this->source->foo(), $value);

		// recursive function
		$format = ':bleh(:bleh(:bleh(:bleh(:bar()))))';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals($this->source->bar(), $value);
		
		// multiple args function
		$format = ':meh(:foo,:bar)';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals($this->source->foo() . $this->source->bar(), $value);
		
	}
	
	public function testValueBuilderCallStatic() {
		
		// call a static method (set class name via litteral)
		$format = array('=Majisti_View_Helper_ValueBuilderMockSource', '_1:myStatic');
		$value = $this->mock->valueBuilder($format, null);
		$this->assertEquals(Majisti_View_Helper_ValueBuilderMockSource::myStatic(), $value);
		
	}
	
	public function testValueBuilderInstance() {
		// create an empty array
		$format = '!array';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals(array(), $value);
				
		// create an empty array 2
		$format = '!array()';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals(array(), $value);
		
		// create an array
		$format = '!array(=1, =2)';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals(array(1, 2), $value);
		
		// create an array 2
		$format = '!array(_value)';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals(array($this->source), $value);
		
		// create a mock object
		$format = '!Majisti_View_Helper_ValueBuilderMockSource';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals(get_class($this->source), get_class($value));
		
		// create a mock object 2
		$format = '!Majisti_View_Helper_ValueBuilderMockSource()';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals(get_class($this->source), get_class($value));
		
		// create a mock object 3 (ignore params)
		$format = '!Majisti_View_Helper_ValueBuilderMockSource(=hello, =world)';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals("Majisti_View_Helper_ValueBuilderMockSource", get_class($value));
		$this->assertEquals($this->source->foo(), $value->foo());
		$this->assertEquals($this->source->bar(), $value->bar());
		
		// create a mock object 4 (with params)
		$format = '!Majisti_View_Helper_ValueBuilderMockSource2(=hello, =world)';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals("Majisti_View_Helper_ValueBuilderMockSource2", get_class($value));
		$this->assertEquals("hello", $value->foo());
		$this->assertEquals("world", $value->bar());
				
		// create a mock object 5 and invoke it's function
		$format = array('!Majisti_View_Helper_ValueBuilderMockSource2(=hello, =world)', '$_foo');
		$value = $this->mock->valueBuilder($format, null);
		$this->assertEquals("hello", $value);
		
	}
	
	public function testValueBuilderBackreference() {
		
		// should return null
		$format = '_0';
		$value = $this->mock->valueBuilder($format, null);
		$this->assertEquals(null, $value);
		
		// same as array('_value');
		$format = '_0';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals($this->source, $value);
		
		// same as array('_value', '_value');
		$format = array('_value', '_0');
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals($this->source, $value);
		
		// test backreference with random values in between
		$format = array('=123', '=456', '_0');
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals($this->source, $value);
		
		// test backreference at a defined index
		$format = array(':foo()', '=123', '_0:bar', '=456', '_3');
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals($this->source->bar(), $value);

		// test backreference to a newly created object
		$format = array('!Majisti_View_Helper_ValueBuilderMockSource2(=Hello, =world)', '=123', '_1$_bar', '=456', '_3');
		$value = $this->mock->valueBuilder($format, null);
		$this->assertEquals("world", $value);
		
		// TODO : do more tests 
		
	}
	
	public function testValueBuilderFunction() {
		
		// simple test
		$format = 'strtoupper(=hello world)';
		$value = $this->mock->valueBuilder($format, null);
		$this->assertEquals("HELLO WORLD", $value);
		
		// recursive test
		$expected = strtoupper(trim(str_pad($this->source->foo(), 20, '*', constant('STR_PAD_BOTH')), '*'));
		$format = 'strtoupper(trim(str_pad(:foo, =20, =*, constant(=STR_PAD_BOTH)), =*))';
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals($expected, $value);
		
	}
	
	public function testValueBuilderArithmetic() {
		
		// simple test (string)
		$format = array('=123', '=456', '_1._2');
		$value = $this->mock->valueBuilder($format, null);
		$this->assertEquals('123456', $value);
		
		// using attributes (string)
		$format = array('_0:foo()', '_0$_bar', '_1._2');
		$value = $this->mock->valueBuilder($format, $this->source);
		$this->assertEquals($this->source->_foo . $this->source->bar(), $value);
		
		// concatenate spaces (string)
		$format = array('=hello', '= ', '=world', '_2._3', '_1._4');
		$value = $this->mock->valueBuilder($format, null);
		$this->assertEquals('hello' . ' ' . 'world', $value);
		
		// simple test (addition)
		$format = array('=1', '=1', '_1+_2');
		$value = $this->mock->valueBuilder($format, null);
		$this->assertSame(1 + 1, $value);

		// simple test (increment)
		$format = array('=1', '_1++');
		$value = $this->mock->valueBuilder($format, null);
		$this->assertSame(2, $value);
		
	}
	
	
	/**
	 * Tests some custom constructs
	 */
	public function testValueBuilderCustom1() {
		
		// create an indexed array
		$format = array('!array(=foo, =bar)', '!array(=hello, =world)', 'array_combine(_1, _2)');
		$value = $this->mock->valueBuilder($format);
		$this->assertEquals(array('foo'=>'hello', 'bar'=>'world'), $value);

		// create an indexed array in one step
		$format = array('array_combine(!array(=foo, =bar), !array(=hello, =world))');
		$value = $this->mock->valueBuilder($format);
		$this->assertEquals(array('foo'=>'hello', 'bar'=>'world'), $value);
		
	}
	
	
}

class Majisti_View_Helper_ValueBuilderMockSource {
	public $_foo = "foo";
	public $_bar = "bar";
	private $_priv = null;  // inaccessible attribute
	
	public function foo() {
		return $this->_foo;
	}
	
	public function bar() {
		return $this->_bar;
	}
	
	public function bleh($value) {
		return $value;
	}
	
	public function meh($value1, $value2) {
		return $value1 . $value2;
	}
	
	private function priv() {
		return $this->_priv;
	}
	
	static public function myStatic() {
		return 'test'; 
	}
}

class Majisti_View_Helper_ValueBuilderMockSource2 extends Majisti_View_Helper_ValueBuilderMockSource {
	
	public function __construct($foo, $bar) {
		$this->_foo = $foo;
		$this->_bar = $bar;
	}
	
}

class SilentErrorHandler {
	static public function handler($errno, $errstr, $errfile, $errline) {
		echo $errno . ': ' . $errstr;
	}
}


Majisti_Test_Runner::run('Majisti_View_Helper_ValueBuildTest');
