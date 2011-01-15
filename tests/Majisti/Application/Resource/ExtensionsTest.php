<?php

namespace Majisti\Application\Resource;

require_once __DIR__ . '/TestHelper.php';

class ExtensionsTest extends \Majisti\Test\TestCase
{
    public function setUp()
    {
        $this->resource = new Extensions();
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

ExtensionsTest::runAlone();
