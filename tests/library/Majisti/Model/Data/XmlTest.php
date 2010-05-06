<?php

namespace Majisti\Model\Data;
use Majisti\I18n\LocaleSession as LocaleSession;

require_once 'TestHelper.php';

/**
 * @desc Test case for Xml class
 * @author Majisti
 */
class XmlTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var Xml
     */
    public $xml;

    /**
     * @var string
     */
    public $xmlPath;

    /**
     * @var bool
     */
    public $useBBCodeMarkup;

    /**
     * @var \Majisti\Util\Collection\Stack
     */
    public $markupStack;

    /**
     * @desc Setups the test case
     */
    public function setUp()
    {
       $this->useBBCodeMarkup  = true;
       $this->xmlPath          = dirname(__FILE__) . '/_files/foo.xml';
       $this->xml              = new Xml($this->xmlPath);
       $this->markupStack      = $this->xml->getMarkupStack();

       LocaleSession::getInstance()->reset()->switchLocale('en');
    }

    /**
     * @desc Tests the pushMarkup() behaviour
     */
    public function testPushMarkup()
    {
       $this->assertEquals(1, $this->markupStack->count());
       $this->assertTrue($this->markupStack->peek()->getParser() instanceof
                                               \Zend_Markup_Parser_Bbcode);

       $this->xml->pushMarkup('Textile');
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
        $this->xml->clearMarkups();
        $this->assertEquals(0, $this->markupStack->count());
    }

    /**
     * @desc Tests that BBCode markup is set to the correct value
     */
    public function testIfBBcodeMarkupValue()
    {
        /* Default and current one is true */
       $this->assertTrue($this->xml->isBBCodeMarkupUsed());

       $this->xml->setBBCodeMarkupUsed(false);
       $this->assertFalse($this->xml->isBBCodeMarkupUsed());
    }

    /**
     * @desc Tests that xml file path getters and setters behave as expected.
     */
    public function testXmlPathGettersAndSetters()
    {
        $this->assertEquals($this->xmlPath, $this->xml->getXmlPath());

        $this->xml->setXmlPath('XmlTesting');
        $this->assertEquals('XmlTesting', $this->xml->getXmlPath());
    }

    /**
     * @desc Testing that the getData() provides data contained in the
     * section linked to the current locale, wich is 'en'.
     */
    public function testThatGetDataWorksWithCurrentLocale()
    {
        $data = $this->xml->getData();
        $this->assertEquals('en', $data->getSectionName());
        $this->assertTrue($data->readOnly());

        $locale = LocaleSession::getInstance();

        /*
         * TODO: Once LocaleSession is refactored and offers the possibility
         * to manually add supported locale, add 'fr' as a supported locale
         * and make it current. Assert that sectionName will be fr IN A NEW TEST.
         *
         */
        $this->markTestIncomplete('Waiting for LocaleSession refactor');
    }

    /**
     * @desc Tests that default locale data is returned if current locale
     * cannot be found.
     */
    public function testThatDataFallsBackToDefaultLocaleIfCurrentLocaleCannotBeFound()
    {
        /**
         * TODO: Make a second XML file without a 'fr' section. With the current
         * locale set to 'fr', assert that it fallbacks on the default section,
         * wich is 'en', IN A NEW TEST.
         */
        $this->markTestIncomplete();
    }
}
XmlTest::runAlone();
