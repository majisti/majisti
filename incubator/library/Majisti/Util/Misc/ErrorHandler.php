<?php

namespace Majisti\Util\Misc;

class ErrorHandler
{
    protected $_errorStr;
    
    protected $_userCallback;
    
    public function startCapture($callback = null)
    {
        $this->reset();
        $this->_userCallback = $callback;
        set_error_handler(array($this, 'errorHandlerFunction'));
    }
    
    public function endCapture()
    {
        restore_error_handler();
    }
    
    public function hasErrorOccured()
    {
        return null !== $this->_errorStr;
    }
    
    public function reset()
    {
        $this->_errorStr = null;
        restore_error_handler();
    }
    
    public function errorHandlerFunction($errno, $errstr, $errfile, $errline) 
    {
        if ( null === $this->_errorStr) {
            $this->_errorStr = $errstr;
        } else {
            $this->_errorStr .= (PHP_EOL . $errstr);
        }
        
        if( null !== $this->_userCallback ) {
            $this->_userCallback($errno, $errstr, $errfile, $errline);
        }
    }
}