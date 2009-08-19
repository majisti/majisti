<?php

/**
 * A simple text test listener for a test result
 *
 * @author Yanick Rochon
 */
class Majisti_Test_PHPUnit_Listener_Text_Simple extends Majisti_Test_PHPUnit_Listener_Abstract
{
	
	/** @var string */
	private $_errorMsg;
	
	/**
	 * Print the text to screen with optional indentation
	 *
	 * @param string $text
	 * @param int $indent (optional) default 0
	 */
	protected function _print($text, $indent = 0)
	{
		$lines = split("\n\r?|\r\n?", $text);
		$lineCount = 0;
		foreach ($lines as $line) {
			if ( $lineCount++ ) echo "\n";
			$tmp = trim($line);
			if ( !empty($tmp) ) {
				if ( $indent ) {
					echo str_repeat(' ', $indent);
				}
				echo $line;}
		}
	}

	/**
	 * Invoked when the a new test suite is being tested.
	 *
	 * @param PHPUnit_Framework_TestSuite $suite
	 */
	public function beforeTestSuite(PHPUnit_Framework_TestSuite $suite)
	{
		$time = new Zend_Date();

		$this->_print($time->get(Zend_Date::TIMES) . " : Test suite started : " . $suite->getName() . "\n");
	}

	/**
	 * Invoked when the a test suite is done testing.
	 *
	 * @param PHPUnit_Framework_TestSuite $suite
	 */
	public function afterTestSuite(PHPUnit_Framework_TestSuite $suite)
	{
		$time = new Zend_Date();

		$this->_print($time->get(Zend_Date::TIMES) . " : Test suite completed : " . $suite->getName() . "\n");

		$result = $this->getResult();

		$executionTime = $result->time() . " sec";

		$testCount = $result->count();
		$failureTestCount = $result->failureCount();
		$skippedTestCount = $result->skippedCount();
		$nonImplementedTestCount = $result->notImplementedCount();


		$this->_print("---\n"
                . "Execution time           : {$executionTime}\n"
		        . "Number of tests executed : {$testCount} "
		        . "({$failureTestCount} failed, {$skippedTestCount} skipped, {$nonImplementedTestCount} not implemented)\n"
		        . "---", 11 );
	}

	/**
	 * Invoked when a test begins, for every test of a test suite or at the
   	 * beginning of the provided test or test case.
	 *
	 * @param PHPUnit_Framework_Test $test
	 */
	public function beforeTest(PHPUnit_Framework_Test $test)
	{
		$time = new Zend_Date();
		$testCount = $this->getResult()->count();

		$this->_errorMsg = null;
		
		$this->_print($time->get(Zend_Date::TIMES) . " : Test #" . $testCount . " : " . $test->getName() . '...');
	}

	/**
	 * Invoked when a test ends, for every test of a test suite or at the
	 * end of the provided test or test case.
	 *
	 * @param PHPUnit_Framework_Test $test
	 */
	public function afterTest(PHPUnit_Framework_Test $test)
	{
		$time = new Zend_Date();
		$testCount = $this->getResult()->count();

		if ( $this->getLastException() )
		{
			$completion = 'interrupted';
		} else {
			$completion = 'OK';
		}

		//$this->_print($time->get(Zend_Date::TIMES) . " : Test #{$testCount} {$completion} : " . $test->getName());
		$this->_print(' ' . $completion . "\n");
		
		if ( $this->_errorMsg ) {
			$this->_print($this->_errorMsg . "\n", 13);
		}
	}

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
	public function failedTest(PHPUnit_Framework_Test $test, $errorType, Exception $e)
	{
		$time = new Zend_Date();

		if ( $this->_errorMsg ) {
			$this->_errorMsg .= "\n";
		}
		
		switch ($errorType)
		{
			case self::TEST_FAILED_INCOMPLETE:
			case self::TEST_FAILED_SKIPPED:
				$this->_errorMsg = "Warning! Test " . $test->getName() . " was " . $errorType . ". Reason : " . $e->getMessage();
				break;
			default:
				$this->_errorMsg = "An error occured in test " . $test->getName() . " (" . $errorType . ")\n"
												 . "Exception : " . $e->getMessage();
				
				$trace = $this->getStackTrace();
				$count = count($trace);
				foreach ($trace as $i => $t) {
					$ref = "{$t['file']}({$t['line']})";
					if ($i+1<$count) {
						$nt = $trace[$i+1];
						if (isset($nt['class'])) {
							$ref .= " - {$nt['class']}{$nt['type']}{$nt['function']}";
						}
					}
					$this->_errorMsg .= "\n  #{$i} - {$ref}";
				}
				break;
		}
	}
}
