<?php

namespace Majisti\Controller\Dispatcher;

require_once 'TestHelper.php';

if( !defined('PHPUnit_MAIN_METHOD') ) {
    define("PHPUnit_MAIN_METHOD", false);
}

/**
 * @desc Test that the multiple dispatcher can dispatch multiple controller
 * directories.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class MultipleTest extends \Zend_Controller_Dispatcher_StandardTest
{
    protected $_filesPath;

    /**
     * @var Multiple
     */
    protected $_dispatcher;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->_filesPath = realpath(dirname(__FILE__) . '/../_files');
    }

    static public function runAlone()
    {
        \Majisti\Test\PHPUnit\TestCase::setClass(__CLASS__);
        \Majisti\Test\PHPUnit\TestCase::runAlone();
    }

    /**
     * Setups the test case
     */
    public function setUp()
    {
        parent::setUp();
        $dispatcher = new Multiple();

        $dispatcher->setControllerDirectory($this->_dispatcher->getControllerDirectory());
        $dispatcher->addControllerDirectory(
            $this->_filesPath . '/anApplicationUsersModule/controllers',
            'users'
        );

        $dispatcher->addFallbackControllerDirectory($this->_filesPath . '/aLibraryUsersModule/controllers', 'users');

        $this->_dispatcher = $dispatcher;
    }

    public function testDispatchNonExistantControllerFallbacksToOtherDirectories()
    {
        $request = new \Zend_Controller_Request_Http();
        $request->setModuleName('users')
                ->setControllerName('list');

        $response = new \Zend_Controller_Response_Cli();

        $this->_dispatcher->dispatch($request, $response);
        $this->assertContains('aLibrary\Controllers\Users_ListController::index', $response->getBody());
    }

    /**
     *
     * @expectedException \Zend_Controller_Dispatcher_Exception
     */
    public function testDispatchNonExistantControllerButExistantInLibraryWithMissingFallbackThrowsException()
    {
        $request = new \Zend_Controller_Request_Http();
        $request->setModuleName('users')
                ->setControllerName('present-only-in-library');

        $this->_dispatcher->resetFallbackControllerDirectory();

        $response = new \Zend_Controller_Response_Cli();

        $this->_dispatcher->dispatch($request, $response);
    }

    public function testNonExistantControllerNameIsDispatchableBecauseOfFallbacks()
    {
        $request = new \Zend_Controller_Request_Http();
        $request->setModuleName('users')
                ->setControllerName('list');

        $response = new \Zend_Controller_Response_Cli();

        $this->assertTrue($this->_dispatcher->isDispatchable($request));
    }

    public function testNonExistantControllerButExistantInLibraryIsNotDispatchableWithMissingFallback()
    {
        $request = new \Zend_Controller_Request_Http();
        $request->setModuleName('users')
                ->setControllerName('presentOnlyInLibrary');

        $response = new \Zend_Controller_Response_Cli();

        $this->assertFalse($this->_dispatcher->isDispatchable($request, $response));
    }

    public function testFallbackControllerDirectories()
    {
        $dispatcher = $this->_dispatcher;

        /* assert single path */
        $dir = '/foo/bar';
        $dispatcher->addFallbackControllerDirectory($dir);
        $dirs = $dispatcher->getFallbackControllerDirectory();
        $this->assertEquals(1, count($dirs));
        $this->assertEquals(array($dir), $dirs);

        $dispatcher->addFallbackControllerDirectory('foo', 'blog');

        /* check that they are available */
        $this->assertTrue($dispatcher->hasFallbackControllerDirectory());
        $this->assertTrue($dispatcher->hasFallbackControllerDirectory(
                $dispatcher->getDefaultModule()));
        $this->assertTrue($dispatcher->hasFallbackControllerDirectory('blog'));
        $this->assertTrue($dispatcher->hasFallbackControllerDirectory('users'));
        $this->assertFalse($dispatcher->hasFallbackControllerDirectory('foo'));

        /* add two more directories */
        $dirs = array('/foo/bar2', '/foo/bar3');
        $dispatcher->addFallbackControllerDirectories($dirs);
        $this->assertEquals(3, count($dispatcher->getFallbackControllerDirectory()));
        $this->assertEquals(3, count($dispatcher->getFallbackControllerDirectory(
                $dispatcher->getDefaultModule())));
        $this->assertEquals(array_merge(array($dir), $dirs),
                $dispatcher->getFallbackControllerDirectory());
        $this->assertEquals(array_merge(array($dir), $dirs),
                $dispatcher->getFallbackControllerDirectory(
                    $dispatcher->getDefaultModule()));

        $this->assertEquals(array('foo'),
                $dispatcher->getFallbackControllerDirectory('blog'));

        /* non existant controller directory */
        $this->assertNull($dispatcher->getFallbackControllerDirectory('foo'));

        /* reset */
        $dispatcher->resetFallbackControllerDirectory();
        $this->assertNull($dispatcher->getFallbackControllerDirectory());
        $this->assertNull($dispatcher->getFallbackControllerDirectory(
                $dispatcher->getDefaultModule()));
        $this->assertNull($dispatcher->getFallbackControllerDirectory('users'));
        $this->assertNull($dispatcher->getFallbackControllerDirectory('foo'));
    }

    public function testSetGetFallbackControllerDirectory()
    {
        $dispatcher = $this->_dispatcher;

        $fallbacks = array(
            'users'     => 'foo',
            'default'   => 'bar'
        );

        $dispatcher->setFallbackControllerDirectories($fallbacks);
        $this->assertEquals($fallbacks, $dispatcher->getFallbackControllerDirectories());
    }

    public function testSetGetControllerDirectory()
    {
        $this->_dispatcher->removeControllerDirectory('users');
        parent::testSetGetControllerDirectory();
    }

    public function testDisableOutputBuffering() {}
}

MultipleTest::runAlone();