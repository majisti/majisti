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

    /**
     * Test that loading a single extension works without throwing exceptions
     */
    public function testValidApplicationLibraryExtensionLoading()
    {
        $resource = $this->resource;

        $resource->setOptions(array(
            'extension' => array(
                'Foo',
            )
        ));

        $resource->init();
    }

    public function testValidMajistixExtensionLoading()
    {
        $resource = $this->resource;

        $resource->setOptions(array(
            'extension' => array(
                'InPlaceEditing'
            )
        ));

        $resource->init();
    }
}

AddonsTest::runAlone();
