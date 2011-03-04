<?php

namespace Majisti\Test\Listener\Simple;

use \Majisti\Test\Util\ServerInfo;

/**
 * @desc Text listener. Used by default by Majisti when running
 * a test within the console (CLI).
 */
class Text extends \Majisti\Test\Listener\DefaultListener
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
        if( $this->out !== NULL ) {
            fwrite($this->out, $buffer);

            if($this->autoFlush) {
                $this->incrementalFlush();
            }
        } else {
            if( !ServerInfo::isCliRunning() ) {
                $buffer = nl2br(htmlspecialchars($buffer));
            }

            print $buffer;

            if( $this->autoFlush ) {
                $this->incrementalFlush();
            }
        }
    }
}