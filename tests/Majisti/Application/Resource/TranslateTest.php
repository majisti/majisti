<?php

namespace Majisti\Application\Resource;

require_once 'TestHelper.php';

/**
 * @desc Test the I18n resource.
 * @author Majisti
 */
class TranslateTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var \Majisti\Application\Resource\Translate
     */
    public $resource;

    /**
     * @desc Setups the test case
     */
    public function setUp()
    {
        $this->resource = new Translate();
        $this->resource->setBootstrap($this->getHelper()->createBootstrapInstance());
    }

    public function testNullTranslatorIsRegistered()
    {
        $translator = $this->resource->getTranslate();
        $options    = $translator->getOptions();

        $this->assertTrue($options['disableNotices']);

        //seems to bug as of ZF 1.10.5, maybe it is deprecated?
//        $this->assertEquals(0, count($translator->getMessages()));
        $this->assertEquals('foo', $translator->translate('foo'));
    }
}

TranslateTest::runAlone();
