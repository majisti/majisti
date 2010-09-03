<?php

namespace Majisti\Application\Resource;

require_once 'TestHelper.php';

/**
 * @desc Test for the Layout resource
 * @author Majisti
 */
class LayoutTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var \Majisti\Application\Resource\Layout
     */
    public $resource;

    /**
     * Setups the test case
     */
    public function setUp()
    {
        $this->resource = new Layout();
    }

    public function testPluginClassIsRegisterdCorrectly()
    {
        $this->assertEquals('Majisti\Controller\Plugin\LayoutSwitcher',
                $this->resource->getLayout()->getPluginClass());
    }
}

LayoutTest::runAlone();
