<?php

require_once 'PHPUnit/Framework/TestListener.php';
require_once 'Zend/Date.php';

abstract class Majisti_Test_PHPUnit_Listener_Abstract implements PHPUnit_Framework_TestListener
{

	/**
	 * A non recuperable error has occured during a test forcing it to abort
   */
	const TEST_FAILED_ERROR = 'error';
	/**
   * The test failed an assertion statement
   */
	const TEST_FAILED_ASSERTION = 'failure';
	/**
   * The test was aborted or did not complete normally
   */
	const TEST_FAILED_INCOMPLETE = 'incomplete';
	/**
   * A test was skipped or could not be executed
   */
	const TEST_FAILED_SKIPPED = 'skipped';

	/**
   * The start time of the test
   *
   * @var Zend_Date
   */
	private $startTime;
	/**
	 * The last exception thrown by the last test. If the last
	 * test was successful, the value should be null
	 *
	 * @var Exception
	 */
	private $lastException;

	/**
	 * The actual test result to which this listener is attached to
	 *
	 * @var PHPUnit_Framework_TestResult
	 */
	private $result;

	/**
	 * Create a new simple text test listener.
	 *
	 * @param PHPUnit_Framework_TestResult $result
	 */
	public function __construct(PHPUnit_Framework_TestResult $result = null)
	{
		$this->result = $result;
	}

	/**
   * Reset all variables and prepare the listener for a next
   * test suite.
   */
	private function _reset() {
		$this->testCount = 0;
		$this->successfulTestCount = 0;
		$this->startTime = new Zend_Date();
		$this->executionTime = new Zend_Date();
		$this->lastException = null;
	}

	/**
	 * Return the last exception for the last executed test.
	 * If the test was successful, the function returns null.
	 *
	 * @return Exception
	 */
	public function getLastException()
	{
		return $this->lastException;
	}

	/**
	 * The the test result
	 *
	 * @return PHPUnit_Framework_TestResult
	 */
	public function getResult()
	{
		return $this->result;
	}
	
	/**
	 * Return a stack trace for the last given exception. The
	 * returned value will be an array filtered of any non relevant
	 * scripts in the stack trace. If there was no last exception,
	 * the method returns an empty array.
	 * 
	 * @return array.
	 */
	protected function getStackTrace() {
		$stackTrace = array();
		
		$ex = $this->getLastException();
		foreach ($ex->getTrace() as $t) {
			if (isset($t['file']) && false===stripos($t['file'],'PEAR')) {
				$stackTrace[] = $t;
			}
		}
		
		return $stackTrace;
	}

	/**
   * Return the current test suite start time. The object returned is
   * a copy of the internal time instance, thus modifying it won't
   * alter the test start timer.
   *
   * @return Zend_Date
   */
	public function getStartTime()
	{
		return clone $this->startTime;
	}

	/**
	 * An error occurred.
	 *
	 * @param  PHPUnit_Framework_Test $test
	 * @param  Exception              $e
	 * @param  float                  $time
	 */
	public function addError( PHPUnit_Framework_Test $test, Exception $e, $time )
	{
		$this->lastException = $e;

		$this->failedTest($test, self::TEST_FAILED_ERROR, $e);
	}

	/**
	 * A failure occurred.
	 *
	 * @param  PHPUnit_Framework_Test                 $test
	 * @param  PHPUnit_Framework_AssertionFailedError $e
	 * @param  float                                  $time
	 */
	public function addFailure( PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time )
	{
		$this->lastException = $e;

		$this->failedTest($test, self::TEST_FAILED_ASSERTION, $e);
	}

	/**
	 * Incomplete test.
	 *
	 * @param  PHPUnit_Framework_Test $test
	 * @param  Exception              $e
	 * @param  float                  $time
	 */
	public function addIncompleteTest( PHPUnit_Framework_Test $test, Exception $e, $time )
	{
		$this->lastException = $e;

		$this->failedTest($test, self::TEST_FAILED_INCOMPLETE, $e);
	}

	/**
	 * Skipped test.
	 *
	 * @param  PHPUnit_Framework_Test $test
	 * @param  Exception              $e
	 * @param  float                  $time
	 * @since  Method available since Release 3.0.0
	 */
	public function addSkippedTest( PHPUnit_Framework_Test $test, Exception $e, $time )
	{
		$this->lastException = $e;

		$this->failedTest($test, self::TEST_FAILED_SKIPPED, $e);
	}

	/**
	 * A test suite started.
	 *
	 * @param  PHPUnit_Framework_TestSuite $suite
	 * @since  Method available since Release 2.2.0
	 */
	public function startTestSuite( PHPUnit_Framework_TestSuite $suite )
	{
		$this->_reset();
		$this->beforeTestSuite($suite);
	}

	/**
	 * A test suite ended.
	 *
	 * @param  PHPUnit_Framework_TestSuite $suite
	 * @since  Method available since Release 2.2.0
	 */
	public function endTestSuite( PHPUnit_Framework_TestSuite $suite )
	{
		$this->afterTestSuite($suite);
	}

	/**
	 * A test started.
	 *
	 * @param  PHPUnit_Framework_Test $test
	 */
	public function startTest( PHPUnit_Framework_Test $test )
	{
		$this->lastException = null;
		$this->beforeTest($test);
	}

	/**
	 * A test ended.
	 *
	 * @param  PHPUnit_Framework_Test $test
	 * @param  float                  $time
	 */
	public function endTest( PHPUnit_Framework_Test $test, $time )
	{
		$this->afterTest($test);
	}

	/**
   * Invoked when the a new test suite is being tested.
   *
   * @param PHPUnit_Framework_TestSuite $suite
   */
	public function beforeTestSuite(PHPUnit_Framework_TestSuite $suite)
	{}

	/**
   * Invoked when the a test suite is done testing.
   *
   * @param PHPUnit_Framework_TestSuite $suite
   */
	public function afterTestSuite(PHPUnit_Framework_TestSuite $suite)
	{}

	/**
   * Invoked when a test begins, for every test of a test suite or at the
   * beginning of the provided test or test case.
   *
   * @param PHPUnit_Framework_Test $test
   */
	abstract public function beforeTest(PHPUnit_Framework_Test $test);

	/**
	 * Invoked when a test ends, for every test of a test suite or at the
	 * end of the provided test or test case.
	 *
	 * @param PHPUnit_Framework_Test $test
	 */
	abstract public function afterTest(PHPUnit_Framework_Test $test);

	/**
   * Invoked when a test fails, for every test of a test suite or when the
   * provided test or test case fails. The type of failure is provided through
   * the parameter $errorType which is one of the constants TEST_FAILED_xxxx.
   * If an exception was thrown, it is provided by the parameter $e.
   *
   * @param PHPUnit_Framework_Test $test    the failed test
   * @param string $errorType               the type of error that has occured
   * @param Exception $e                    the exception thrown (if any)
   */
	abstract public function failedTest(PHPUnit_Framework_Test $test, $errorType, Exception $e);

}