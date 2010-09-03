<?php

namespace Majisti\Test\Util;

/**
 * @desc Outputs a better stack trace for unit tests.
 *
 * @author Majisti
 */
class Filter extends \PHPUnit_Util_Filter
{
    /**
     * Filters stack frames from PHPUnit classes.
     *
     * @param  Exception $e
     * @param  boolean   $filterTests
     * @param  boolean   $asString
     * @return string
     */
    public static function getFilteredStacktrace(\Exception $e, $filterTests = true, $asString = true,
        $fileCallback = null, $lineCallback = null)
    {
        if ($asString === true) {
            $filteredStacktrace = '';
        } else {
            $filteredStacktrace = array();
        }

        $eTrace = $e->getTrace();

        if (!self::frameExists($eTrace, $e->getFile(), $e->getLine())) {
            array_unshift(
              $eTrace, array('file' => $e->getFile(), 'line' => $e->getLine())
            );
        }

        foreach ($eTrace as $frame) {
            if (!self::$filter || (isset($frame['file']) && !static::isFiltered($frame['file'], $filterTests, true))) {
                if ($asString === true) {
                    $filteredStacktrace .= sprintf(
                      "%s:%s\n",
                    
                        null !== $fileCallback
                        ? $fileCallback($frame['file'])
                        : $frame['file'],
                      isset($frame['line']) 
                          ? null !== $lineCallback
                              ? $lineCallback($frame['line'])
                              : $frame['line'] 
                          : '?'
                    );
                } else {
                    $filteredStacktrace[] = $frame;
                }
            }
        }

        return $filteredStacktrace;
    }
}