<?php

namespace Majisti\Test\Util;

/**
 * @desc Gives the same timer as PHPUnit, but with milliseconds as well.
 *
 * @author Majisti
 */
class Timer extends \PHPUnit_Util_Timer
{
    /**
     * @desc Formats elapsed time (in milliseconds) to a string.
     *
     * @param  float $time
     * @return float
     */
    public static function millisecondsToTimeString($time)
    {
        $buffer = '';
        
        $hours = sprintf('%02d',
                        ($time >= 3600) ? floor($time / 3600) : 0);
        $minutes = sprintf('%02d',
                          ($time >= 60)   ? floor($time /   60)
                          - 60 * $hours : 0);
        $seconds = sprintf('%02d',
                          $time - 60 * 60 * $hours - 60 * $minutes);
        $milliseconds = round(sprintf('%f',
                               ($time - $seconds) * 1000));
        
        if ($hours == 0 && $minutes == 0) {
            $seconds = sprintf('%1d', $seconds);

            $buffer .= $seconds . ' second';

            if ($seconds != '1') {
                $buffer .= 's';
            }
            
            $buffer .= ' ' . $milliseconds . ' millisecond';
            
            if( (int)$milliseconds != '1' ) {
                $buffer .= 's';
            }
        } else {
            if ($hours > 0) {
                $buffer = $hours . ':';
            }

            $buffer .= $minutes . ':' . $seconds;
        }

        return $buffer;
    }
}