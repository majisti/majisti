<?php

namespace Majisti\Application;

require_once __DIR__ . '/TestHelper.php';

/**
 * @desc Assert that the application was instanciated correctly.
 * It was already instanciated in the TestHelper
 *
 * @author Majisti
 */
class ManagerTest extends \Majisti\Test\TestCase
{
    public $manager;

    public function setUp()
    {
        $this->manager = new Manager(array(
            'majisti' => array(
                'app' => array(
                    'path'      => __DIR__ . '/_project',
                    'env'       => 'development',
                    'namespace' => 'MajistiT',
                )
            )
        ));
    }

    public function testThatPropertyHandlerIsLoadedByDefault()
    {
        $options = $this->manager->getApplication()->getBootstrap()->getOptions();
        $this->assertEquals('foo', $options['property']['test']['foo']);
    }
}

ManagerTest::runAlone();
