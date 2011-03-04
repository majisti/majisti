<?php

namespace Majisti\Model;

use Majisti\Util\Model\Collection;

use \Majisti\Util\Model\Collection\Stack as Stack;
use \Majisti\Application\Locales         as Locales;

class Xml
{
    /**
     * @var Zend_Config_Xml
     */
    protected $_data;

    /**
     * @var string
     */
    protected $_xmlPath;

    /**
     * @var bool
     */
    protected $_useBBCodeMarkup;

    /**
     * @var Stack
     */
    protected $_markupStack;

    /**
     * @var \Zend_Locale
     */
    protected $_locale;

    /**
     * @var Locales
     */
    static protected $_locales;

    /**
     * @desc contructor
     */
    public function  __construct($xmlPath = null, $useBBCodeMarkup = true)
    {
        $this->_xmlPath         = $xmlPath;
        $this->_useBBCodeMarkup = $useBBCodeMarkup;
    }

    /**
     * @desc Sets the locale.
     *
     * @param \Zend_Locale $locale The locale
     */
    public function setLocale(\Zend_Locale $locale)
    {
        $this->_locale = $locale;
    }

    /**
     * @desc Returns the locale.
     *
     * @return \Zend_Locale
     */
    public function getLocale()
    {
        if( null === $this->_locale ) {
            $this->_locale = static::getLocales()->getCurrentLocale();
        }

        return $this->_locale;
    }

    /**
     * @desc Sets the application locales object.
     *
     * @param Locales $locales
     */
    static public function setLocales(Locales $locales)
    {
        static::$_locales = $locales;
    }

    /**
     * @desc Returns the default locale.
     *
     * @return Locales The application locales object.
     */
    static public function getLocales()
    {
        return static::$_locales;
    }

    /**
     * @desc Pushes a markup to the stack
     *
     * @param $parser The parser class
     * @param $renderer The renderer class
     */
    public function pushMarkup($parser, $renderer = 'Html')
    {
        $this->getMarkupStack()->push(
            \Zend_Markup::factory((string)$parser, (string)$renderer)
        );
    }

    /**
     * @desc Clears the markups
     */
    public function clearMarkups()
    {
        $this->_markupStack->clear();
    }

    /**
     * @desc Returns the markup stack
     * @return \Majisti\Util\Model\Stack
     */
    public function getMarkupStack()
    {
        if( null === $this->_markupStack ) {
            $this->_markupStack = new Stack();

            if( $this->isBBCodeMarkupUsed() ) {
                /* parse with BBCode Zend_Markup */
                $bbCode = \Zend_Markup::factory('Bbcode');
                $bbCode->addMarkup(
                    'br',
                    \Zend_Markup::REPLACE,
                    array(
                        'replace'   => '<br />',
                        'group'     => 'inline',
                    )
                );

                $this->_markupStack->push($bbCode);
            }
        }

        return $this->_markupStack;
    }

    /**
     * @desc Whether the BBCode to Html Markup is used.
     *
     * @return bool True if BBCode parser to Html Renderer Markup is used.
     */
    public function isBBCodeMarkupUsed()
    {
        return $this->_useBBCodeMarkup;
    }

    /**
     * @desc BBCodeMarkup setter
     */
    public function setBBCodeMarkupUsed($BBCodeMarkupUsed)
    {
        $this->_useBBCodeMarkup = $BBCodeMarkupUsed;
    }

    /**
     * @desc Returns the xml file path
     * @return String the xml file path
     */
    public function getXmlPath()
    {
       return $this->_xmlPath;
    }

    /**
     * @desc Xml path file setter
     */
    public function setXmlPath($xmlPath)
    {
        $this->_xmlPath = $xmlPath;
    }

    /**
     * @desc Retrieve internal data according to current language,
     * assuming default language if the current language is not found.
     */
    public function getData()
    {
        $locales = static::getLocales();

        if( null === $this->_data ||
                $this->_locale !== $locales->toString() ) {

            /* load current locale section */
            $this->_locale = $locales->toString();
            try {
                $data = new \Zend_Config_Xml(
                    $this->_xmlPath,
                    $locales->toString(),
                    array('allowModifications' => true)
                );
            /* section could not be found, fallback to default locale */
            } catch( \Zend_Config_Exception $e ) {
                $data = new \Zend_Config_Xml(
                    $this->_xmlPath,
                    $locales->getDefaultLocale()->getLanguage(),
                    array('allowModifications' => true)
                );
            }

            /* apply markup stack */
            $stack = $this->getMarkupStack();
            if( !$stack->isEmpty() ) {
                $markup = new \Majisti\Config\Handler\Markup(
                    $stack->toArray());

                $data = $markup->handle($data);
            }

            $data->setReadOnly();

            $this->_data = $data;
        }

        return $this->_data;
    }
}
