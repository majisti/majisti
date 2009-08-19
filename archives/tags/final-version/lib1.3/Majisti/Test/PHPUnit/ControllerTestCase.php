<?php

/**
 * Zend framework controller test case wrapper for Majisti applications
 *
 * @author Yanick Rochon
 */

/**
 * To setup a Majisti application controller test case, one must do the following :
 *
 * 1) Setup the bootstrap by setting the boostrap property with a callback such as
 *
 *       $bootstrap = array('MyProject_Bootstrap', 'getInstance');
 *
 *    If the bootstrap file should be included inside the test case :
 *
 *       public function setUp() {
 * 					include_once 'path/to/bootstrap.php';
 * 					$this->bootstrap = array('MyProject_Bootstrap', 'getInstance');
 * 					parent::setUp();  // necessary to initialize the test case
 *       }
 *
 */
abstract class Majisti_Test_PHPUnit_ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{

}
