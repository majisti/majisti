<?php

namespace Majisti\Application;

use Majisti\Config\Configuration;

require_once __DIR__ . '/TestHelper.php';

/**
 * @desc Assert that the application was instanciated correctly.
 * It was already instanciated in the TestHelper
 *
 * @author Majisti
 */
class ManagerTest extends \Majisti\Test\TestCase
{
    public $conf;

    public $manager;

    public function setUp()
    {
        $this->conf = new Configuration(array(
            'majisti' => array(
                'app' => array(
                    'path'      => __DIR__ . '/_project',
                    'env'       => 'development',
                    'namespace' => 'MajistiT',
                )
            )
        ));

        $this->manager = new Manager($this->conf->getOptions()->toArray());
    }

    public function testThatPropertyHandlerIsLoadedByDefault()
    {
        $options = new Configuration(
            $this->manager->getApplication()->getBootstrap()->getOptions());

        $this->assertEquals('foo', $options->find('property.test.foo'));
    }

    public function testUrlsAsCliPhpSapi()
    {
        $_SERVER['REQUEST_URI']     = '';
        $_SERVER['DOCUMENT_ROOT']   = '';
        $_SERVER['HTTP_HOST']       = '';
        $_SERVER['SERVER_NAME']     = '';
        $_SERVER['SCRIPT_FILENAME'] = 'majisti.php';
        $_SERVER['SCRIPT_NAME']     = 'majisti.php';
        $_SERVER['PHP_SELF']        = 'majisti.php';

        $this->conf->extend(array(
            'majisti' => array(
                'app' => array(
                    'url'     => '/foo',
                    'baseUrl' => '/foo',
                )
            )
        ));

        $manager = new Manager($this->conf->getOptions()->toArray());

        $options = new Configuration(
            $manager->getApplication()->getBootstrap()->getOptions());

        $this->assertEquals('/foo', $options->find('majisti.app.url'));
        $this->assertEquals('/foo', $options->find('majisti.app.baseUrl'));
        $this->assertEquals('/foo/majisti', $options->find('majisti.url'));
    }
}

ManagerTest::runAlone();
