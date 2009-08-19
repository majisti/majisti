<?php

require_once 'PHPUnit/Framework/TestListener.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/Framework/TestResult.php';

/**
 * A simple test runner
 * 
 * @author Yanick Rochon, Steven Rosato
 */
class Majisti_Test_Runner
{
	/** @var Majisti_Test_PHPUnit_Listener_Abstract */
	static private $_defaultListener;
	
	/**
	 * Run the test suite. If no listeners is given, the test will take a
	 * Majisti_Test_PHPUnit_Listener_Text_Simple as default listener.
	 *
	 * @param string $className
	 * @param mixed $listeners (optional) (default = null) An array or a single PHPUnit_Framework_TestListener
	 * 
	 * @return PHPUnit_Framework_TestResult
	 */
	static public function run( $className, $listeners = null )
	{
		$runner = new self($className);
		
		if( $listeners == null ) {
			// set content header to text if possible
			if ( !headers_sent() ) {
				header('Content-type: text/plain; charset=utf-8');
			}

			/* Run a simple text adapter if no default listener was setup or given */
			if( self::$_defaultListener == null ) {
				$runner->_result->addListener(new Majisti_Test_PHPUnit_Listener_Text_Simple($runner->_result));
			} else {
				$runner->_result->addListener(self::$_defaultListener);
			}
		/* Add the listener(s) and check that they are or it is an instance of PHPUnit_Framework_TestListener */
		} else if( is_array($listeners) ) {
			foreach ($listeners as $listener) {
				if( !($listener instanceof PHPUnit_Framework_TestListener) ) {
					throw new Majisti_Test_Exception("Passed listeners must all be instances of PHPUnit_Framework_TestListener");
				}
				$runner->_result->addListener($listener);
			}
		} else if( !($listeners instanceof PHPUnit_Framework_TestListener) ) {
			throw new Majisti_Test_Exception("Passed listener must be an instance of PHPUnit_Framework_TestListener");
		} else {
			$runner->_result->addListener($listeners);
		}

		return $runner->_suite->run($runner->_result);
	}
	
	/**
	 * Sets the default listener. If the static method run() is called, this listener will be used.
	 *
	 * @param Majisti_Test_PHPUnit_Listener_Abstract $listener
	 */
	static public function setDefaultListener(Majisti_Test_PHPUnit_Listener_Abstract $listener) 
	{
		self::$_defaultListener = $listener;
	}
	
	/**
	 * Returns a clone of the default listener
	 *
	 * @return Majisti_Test_PHPUnit_Listener_Abstract | null if no default listener was previously setup
	 */
	static public function getDefaultListener()
	{
		return clone self::$_defaultListener;
	}

	/**
	 * The test suite to run
	 *
	 * @var PHPUnit_Framework_TestSuite
	 */
	private $_suite;

	/**
	 * The test result of the suite
	 *
	 * @var PHPUnit_Framework_TestResult
	 */
	private $_result;

	/**
	 * Construct a new test runner
	 *
	 * @param string $className The name of the test suite class
	 */
	public function __construct( $className )
	{
		// Create a test suite that contains the tests
		// from the ArrayTest class.
		$this->_suite 	= new PHPUnit_Framework_TestSuite($className);
		$this->_result 	= new PHPUnit_Framework_TestResult();
	}

	/**
	 * Get the test result of this runner
	 *
	 * @return PHPUnit_Framework_TestResult
	 */
	public function getResult()
	{
		return $this->_result;
	}

	/**
	 * Get the test suite of this runner
	 *
	 * @return PHPUnit_Framework_TestSuite
	 */
	public function getSuite()
	{
		// Run the tests.
		return $this->_suite;
	}
}