<?php

namespace Majisti\Application\Resource;

require_once 'TestHelper.php';

/**
 * @desc Test the I18n resource.
 * @author Majisti
 */
class I18nTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var \Majisti\Application\Resource\I18n
     */
    public $resource;

    /**
     * @desc Setups the test case
     */
    public function setUp()
    {
        $this->resource = new I18n();
    }

    public function testNullTranslatorIsRegistered()
    {
        $translator = $this->resource->getTranslator();
        $options    = $translator->getOptions();

        $this->assertTrue($options['disableNotices']);
        $this->assertEquals(0, count($translator->getMessages()));
        $this->assertEquals('foo', $translator->translate('foo'));
    }
}

I18nTest::runAlone();