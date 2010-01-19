<?php

namespace Majisti\Loader;

require_once 'TestHelper.php';

/**
 * @desc Autoloader test case.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AutoloaderTest extends \Majisti\Test\PHPUnit\TestCase
{
    protected static $_class = __CLASS__;

    /**
     * @var Autoloader
     */
    private $_autoloader;

    public $serverDir;

    /**
     * @desc Prepares the environment by changing the current directory.
     */
    protected function setUp()
    {
        $this->serverDir = getcwd();
        chdir(realpath(dirname(__FILE__) . '/../'));
        $this->_autoloader = new Autoloader();
    }

    /**
     * @desc Resets the current directory.
     */
    protected function tearDown()
    {
        chdir($this->serverDir);
    }

    /**
     * @desc Tests Autoloader->autoload()
     *
     * @expectedException \Majisti\Loader\Exception
     */
    public function testAutoloadShouldThrowException()
    {
        $this->_autoloader->autoload('\Some\Non\Existant\Namespace\Class');
    }

    /**
     * @desc Tests Autoloader->autoload()
     *
     * @expectedException \Majisti\Loader\Exception
     */
    public function testAutoloadShouldThrowExceptionOnEmptyString()
    {
        $this->_autoloader->autoload('');
    }

    /**
     * @desc Tests Autoloader->autoload()
     */
    public function testAutoloadShouldLoadThoseClassesCorrectly()
    {
        $this->_autoloader->autoload("Config\Handler\PropertyTest");
        $this->_autoloader->autoload("Majisti\Loader\AutoloaderTest");
        $this->_autoloader->autoload("Majisti\Test\PHPUnit\TestCase");
    }
}

AutoloaderTest::runAlone();
