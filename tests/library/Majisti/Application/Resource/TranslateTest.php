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
    }

    public function testNullTranslatorIsRegistered()
    {
        $translator = $this->resource->getTranslate();
        $options    = $translator->getOptions();

        $this->assertTrue($options['disableNotices']);
        $this->assertEquals(0, count($translator->getMessages()));
        $this->assertEquals('foo', $translator->translate('foo'));
    }
}

TranslateTest::runAlone();
