<?php

namespace Majisti\Test\Listener\Simple;

/**
 * @desc Html listener that outputs colors. This listener is used by default
 * by Majisti when running test via the browser.
 *
 * @author Majisti
 */
class Html extends \Majisti\Test\DefaultListener
{
    public function __construct($out = null, $verbose = false, $debug = false)
    {
        parent::__construct($out, $verbose, false, $debug);
    }
    
     /**
     * @param  string $buffer
     */
    public function write($buffer)
    {
        if ($this->out !== NULL) {
            fwrite($this->out, $buffer);

            if ($this->autoFlush) {
                $this->incrementalFlush();
            }
        } else {
            if (PHP_SAPI != 'cli') {
                $buffer = nl2br($buffer);
            }

            print $buffer;

            if ($this->autoFlush) {
                $this->incrementalFlush();
            }
        }
    }
    
    /**
     * @param  PHPUnit_Framework_TestFailure $defect
     */
    protected function printDefectTrace(\PHPUnit_Framework_TestFailure $defect)
    {
        $fileCallback = function($file) {
            return "<a href=\"#\">{$file}</a>";
        };
        
        $lineCallback = function($line) {
            return "<b>{$line}</b>";
        };
        
        $this->write(
          htmlspecialchars($defect->getExceptionAsString()) .
          \Majisti\Test\Util\Filter::getFilteredStacktrace(
            $defect->thrownException(),
            false,
            true,
            $fileCallback,
            $lineCallback
          )
        );
    }
    
    /**
     * An error occurred.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     */
    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->writeProgress('<span style="color:red">E</span>');
        $this->lastTestFailed = true;
    }

    /**
     * A failure occurred.
     *
     * @param  PHPUnit_Framework_Test                 $test
     * @param  PHPUnit_Framework_AssertionFailedError $e
     * @param  float                                  $time
     */
    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->writeProgress('<span style="color:red">F</span>');
        $this->lastTestFailed = true;
    }

    /**
     * Incomplete test.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     */
    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->writeProgress('<span style="color:brown">I</span>');
        $this->lastTestFailed = true;
    }

    /**
     * Skipped test.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     * @since  Method available since Release 3.0.0
     */
    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->writeProgress('<span style="color:orange">S</span>');
        $this->lastTestFailed = true;
    }
}
