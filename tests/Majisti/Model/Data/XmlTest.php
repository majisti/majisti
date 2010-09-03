<?php

namespace Majisti\Model\Data;
use Majisti\Application\Locales as Locales;

require_once 'TestHelper.php';

/**
 * @desc Test case for Xml class
 * @author Majisti
 */
class XmlTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var Xml
     */
    public $xmlWithFr;

    /**
     * @var string
     */
    public $fooPath;

    /**
     * @var string
     */
    public $barPath;

    /**
     * @var bool
     */
    public $useBBCodeMarkup;

    /**
     * @var \Majisti\Util\Collection\Stack
     */
    public $markupStack;

    /**
     * @var Locales
     */
    public $locales;

    /**
     * @var \Zend_Locale
     */
    public $en;

    /**
     * @var \Zend_Locale
     */
    public $fr;

    /**
     * @desc Setups the test case
     */
    public function setUp()
    {
       $this->useBBCodeMarkup  = true;

       $this->fooPath = __DIR__ . '/_files/foo.xml';
       $this->barPath = __DIR__ . '/_files/bar.xml';

       $this->xmlWithFr     = new Xml($this->fooPath);
       $this->xmlWithoutFr  = new Xml($this->barPath);

       $this->markupStack   = $this->xmlWithFr->getMarkupStack();

       $this->en = new \Zend_Locale('en');
       $this->fr = new \Zend_Locale('fr');

       $this->locales = Locales::getInstance();
       $this->locales->addLocales(array($this->en, $this->fr));
       $this->locales->switchLocale($this->en);
    }

    /**
     * @desc Tests that the pushMarkup() adds the markup to the stack.
     */
    public function testPushMarkup()
    {
       $this->assertEquals(1, $this->markupStack->count());
       $this->assertTrue($this->markupStack->peek()->getParser() instanceof
                                               \Zend_Markup_Parser_Bbcode);

       $this->xmlWithFr->pushMarkup('Textile');
       $this->assertEquals(2, $this->markupStack->count());

       $renderer = $this->markupStack->peek();
       $this->assertTrue($renderer->getParser() instanceof
                                                \Zend_Markup_Parser_Textile);
    }

    /**
     * @desc Tests that markup stack is empty when cleared
     */
    public function testClearMarkupStack()
    {
        $this->xmlWithFr->clearMarkups();
        $this->assertEquals(0, $this->markupStack->count());
    }

    /**
     * @desc Tests that BBCode markup is set to the correct value
     */
    public function testIfBBcodeMarkupValue()
    {
        /* Default and current one is true */
       $this->assertTrue($this->xmlWithFr->isBBCodeMarkupUsed());

       $this->xmlWithFr->setBBCodeMarkupUsed(false);
       $this->assertFalse($this->xmlWithFr->isBBCodeMarkupUsed());
    }

    /**
     * @desc Tests that xml file path getters and setters behave as expected.
     */
    public function testXmlPathGettersAndSetters()
    {
        $this->assertEquals($this->fooPath, $this->xmlWithFr->getXmlPath());

        $this->xmlWithFr->setXmlPath('XmlTesting');
        $this->assertEquals('XmlTesting', $this->xmlWithFr->getXmlPath());
    }

    /**
     * @desc Testing that the getData() provides data contained in the
     * section linked to the current locale, wich is 'en'.
     */
    public function testThatGetDataWorksWithCurrentLocale()
    {
        $locales = $this->locales;

        $data = $this->xmlWithFr->getData();
        $this->assertEquals('en', $data->getSectionName());
        $this->assertTrue($data->readOnly());

        $locales->switchLocale($this->fr);

        $data = $this->xmlWithFr->getData();
        $this->assertEquals('fr', $data->getSectionName());
    }

    /**
     * @desc Tests that default locale data is returned if current locale
     * cannot be found.
     */
    public function testThatDataFallsBackToDefaultLocaleIfCurrentLocaleCannotBeFound()
    {
        $locales = $this->locales;
        $fr      = $this->fr;

        $locales->switchLocale($fr);
        $data    = $this->xmlWithoutFr->getData();
        $this->assertEquals('en', $data->getSectionName());
    }
}
XmlTest::runAlone();
