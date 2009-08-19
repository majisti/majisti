<?php
/**
 * Majisti Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@majisti.com so we can send you a copy immediately.
 *
 * @category   Majisti
 * @package    Majisti_View
 * @copyright  Copyright (c) 2009 Majisti Inc. (http://www.majisti.com)
 * @license    http://framework.majisti.com/license/new-bsd     New BSD License
 */


/**
 * Interface class for Zend_Model compatible model structure implementations
 *
 * @category   Majisti
 * @package    Majisti_View
 * @copyright  Copyright (c) 2009 Majisti Inc. (http://www.majisti.com)
 * @license    http://framework.majisti.com/license/new-bsd     New BSD License
 */
interface Majisti_View_Interface 
{
    
    /**
     * Assign a variable to the model
     *
     * @param string $key The variable name.
     * @param mixed $val The variable value.
     * @return void
     */
    public function __set($key, $val);

    /**
     * Allows testing with empty() and isset() to work
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key);

    /**
     * Allows unset() on object properties to work
     *
     * @param string $key
     * @return void
     */
    public function __unset($key);

}