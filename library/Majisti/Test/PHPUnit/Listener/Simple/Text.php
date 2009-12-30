<?php

namespace Majisti\Test\PHPUnit\Listener\Simple;

use Majisti\Test\PHPUnit;

class Text extends PHPUnit\DefaultListener
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
                $buffer = nl2br(htmlspecialchars($buffer));
            }

            print $buffer;

            if ($this->autoFlush) {
                $this->incrementalFlush();
            }
        }
    }
}