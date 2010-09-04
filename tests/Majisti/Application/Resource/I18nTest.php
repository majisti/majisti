<?php

namespace Majisti\Application\Resource;

require_once 'TestHelper.php';

class I18nTest extends \Majisti\Test\TestCase
{
    public function setUp()
    {
        $this->resource = new I18n();
        $this->resource->setBootstrap(
            $this->getHelper()->createBootstrapInstance()
        );

        $this->locales = \Majisti\Application\Locales::getInstance();
        $locale = new \Zend_Locale('en');
        $this->locales->addLocale($locale)->setDefaultLocale($locale);
    }

    public function testFormTranslationEnabled()
    {
        $this->resource->init();

        $t = \Zend_Form::getDefaultTranslator();
        $this->assertNotNull($t);

        $options = (object)$t->getOptions();
        $locales = \Majisti\Application\Locales::getInstance();

        $this->assertEquals($this->getHelper()->getMajistiPath() . '/resources/languages',
                $options->content);
        $this->assertEquals($this->locales->getCurrentLocale()->getLanguage(),
                $options->locale);
        $this->assertEquals(\Zend_Translate_Adapter::LOCALE_DIRECTORY,
                $options->scan);
    }
}

I18nTest::runAlone();
