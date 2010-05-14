<?php

namespace Majisti\Model\Data;

use Majisti\Util\Model\Collection;

use \Majisti\Util\Model\Collection\Stack    as Stack;
use \Majisti\Application\Locales            as Locales;

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
     * @var string
     */
    protected $_locale;

    /**
     * @desc contructor
     */
    public function  __construct($xmlPath = null, $useBBCodeMarkup = true)
    {
        $this->_locale          = Locales::getInstance()->toString();
        $this->_xmlPath         = $xmlPath;
        $this->_useBBCodeMarkup = $useBBCodeMarkup;
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
                $bbCode->addTag(
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
        $locale = Locales::getInstance();

        if( null === $this->_data ||
                $this->_locale !== $locale->toString() ) {

            /* load current locale section */
            $this->_locale = $locale->toString();
            try {
                $data = new \Zend_Config_Xml(
                    $this->_xmlPath,
                    $locale->toString(),
                    array('allowModifications' => true)
                );
            /* section could not be found, fallback to default locale */
            } catch( \Zend_Config_Exception $e ) {
                $data = new \Zend_Config_Xml(
                    $this->_xmlPath,
                    $locale->getDefaultLocale()->toString(),
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
