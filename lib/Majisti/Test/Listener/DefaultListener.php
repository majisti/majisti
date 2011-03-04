<?php

namespace Majisti\Test\Listener;

/**
 * @desc Default listener for Majisti. Writes timer with miliseconds.
 *
 * @author Majisti
 */
class DefaultListener extends \PHPUnit_TextUI_ResultPrinter
{
    /**
     * @param  float   $timeElapsed
     */
    protected function printHeader($timeElapsed)
    {
        $this->write(
          sprintf(
            "%sTime: %s\n\n",
            $this->verbose ? "\n" : "\n\n",
            \Majisti\Test\Util\Timer::millisecondsToTimeString($timeElapsed)
          )
        );
    }
}