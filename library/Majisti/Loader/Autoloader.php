<?php

namespace Majisti\Loader;

/**
 * @desc Fast implementation of PHP 5.3+ namespace autoloading. Zend will
 * probably release something so this class is lightly tested and
 * will surely eventually get deprecated. This autoloader can only
 * load \Namespaced\Elements and not Underscored_Elements.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Autoloader implements \Zend_Loader_Autoloader_Interface
{
    protected $_includeFileErrorStr;

    public function autoload($class)
    {
        if( empty($class) ) {
            throw new Exception("Class can't be empty");
        }

        $loaded = false;
        $parts = explode('\\', $class);

        if( count($parts) === 1 ) {
            return false;
        }

        $path       = implode(DIRECTORY_SEPARATOR, $parts);
        $phpScript  = $path . '.php';

        if( strlen($path) > 0 ) {
            set_error_handler(array($this, 'includeFileErrorHandler'));
            include_once $phpScript;
            restore_error_handler();

            if ( null === $this->_includeFileErrorStr ) {
               $loaded = true;
            }
        }

        if( !$loaded ) {
            require_once dirname(__FILE__) . '/Exception.php';
            throw new Exception($this->_includeFileErrorStr);
        }
    }

    public function includeFileErrorHandler($errno, $errstr, $errfile, $errline)
    {
        if ($this->_includeFileErrorStr === null) {
            $this->_includeFileErrorStr = $errstr;
        } else {
            $this->_includeFileErrorStr .= (PHP_EOL . $errstr);
        }
    }
}