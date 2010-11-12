<?php

namespace Majisti\Application\Resource;

require_once 'TestHelper.php';

class AddonsTest extends \Majisti\Test\TestCase
{
    public function setUp()
    {
        $this->resource = new Addons();
        $this->resource->setBootstrap($this->getHelper()
             ->createBootstrapInstance());
    }

    public function testInit()
    {
        $appPath  = $this->getHelper()->getOption('majisti.app.path');
        $resource = $this->resource;

        $resource->setOptions(array(
            'ext' => array(
                'paths' => array(
                    $appPath . '/library/extensions'
                ),
                'FooExtension'
            ),
            'modules' => array(
                'paths' => array(
                    $appPath . '/library/modules'
                ),
                'auth'
            )
        ));

        $resource->init();
    }
}

AddonsTest::runAlone();
