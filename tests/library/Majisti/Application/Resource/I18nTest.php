<?php

namespace Majisti\Application\Resource;

require_once 'TestHelper.php';

class I18nTest extends \Majisti\Test\TestCase
{
    public $resource;

    static protected $_class = __CLASS__;

    public function setUp()
    {
        $this->resource = new I18n();
    }

    public function testFormTranslationEnabled()
    {
        $this->resource->init();

        $t = \Zend_Form::getDefaultTranslator();
        $this->assertNotNull($t);

        $options = (object)$t->getOptions();
        $locales = \Majisti\Application\Locales::getInstance();

        $this->assertEquals(MAJISTI_ROOT . '/resources/languages',
                $options->content);
        $this->assertEquals($locales->getCurrentLocale()->getLanguage(),
                $options->locale);
        $this->assertEquals(\Zend_Translate_Adapter::LOCALE_DIRECTORY,
                $options->scan);
    }
}

I18nTest::runAlone();
