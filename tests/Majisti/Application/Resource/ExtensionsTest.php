<?php

namespace Majisti\Application\Resource;

require_once __DIR__ . '/TestHelper.php';

/**
 * @desc Test the extensions resource.
 *
 * @author Majisti
 */
class ExtensionsTest extends \Majisti\Test\TestCase
{
    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function setUp()
    {
        $this->resource = new Extensions();
        $this->resource->setBootstrap($this->getHelper()
             ->createBootstrapInstance());
    }

    /**
     * Test that loading a single extension works without throwing exceptions
     */
    public function testImplicitLoading()
    {
        $resource = $this->resource;

        $resource->setOptions(array('Foo'));

        $manager = $resource->init();
        $this->assertTrue($manager->isExtensionLoaded('Foo'));
    }

    public function testImplicitLoadingWithOptionsOnly()
    {
        $resource = $this->resource;

        $resource->setOptions(array(
            'Foo' => array(
                'anOption' => 'aValue'
            )
        ));

        $manager = $resource->init();
        $this->assertTrue($manager->isExtensionLoaded('Foo'));
    }

    public function testOptionsAreAllPassedToExtensionBootstrap()
    {
        $resource = $this->resource;

        $resource->setOptions(array(
            'Foo' => array(
                'enabled'  => 1,
                'anOption' => 'aValue',
            )
        ));
        $manager = $resource->init();

        $extOptions = $manager->getLoadedExtensionBootstrap('Foo')->getOptions();

        $this->assertTrue($manager->isExtensionLoaded('Foo'));
        $this->assertArrayNotHasKey('enabled', $extOptions);
        $this->assertArrayHasKey('anOption', $extOptions);
        $this->assertEquals('aValue', $extOptions['anOption']);
    }

    public function testExplicitLoading()
    {
        $resource = $this->resource;

        $resource->setOptions(array(
            'Foo' => array(
                'enabled' => 1
            )
        ));
        $manager = $resource->init();

        $this->assertTrue($manager->isExtensionLoaded('Foo'));
    }

    public function testExplicitelyDisabledExtensionWillNotLoad()
    {
        $resource = $this->resource;

        $resource->setOptions(array(
            'Foo' => array(
                'enabled' => 0
            )
        ));
        $manager = $resource->init();

        $this->assertFalse($manager->isExtensionLoaded('Foo'));
    }

}

ExtensionsTest::runAlone();
