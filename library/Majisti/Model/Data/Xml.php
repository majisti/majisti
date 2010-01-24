<?php

namespace Majisti\Model\Data;

use Majisti\Util\Model\Collection;

use \Majisti\Util\Model\Collection\Stack    as Stack;
use \Majisti\I18n\LocaleSession             as LocaleSession;

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
    protected $_useBBCodeMarkup = true;

    /**
     * @var Stack
     */
    protected $_markupStack;

    /**
     * @desc Pushes a markup to the stack
     *
     * @param $parser The parser class
     * @param $renderer The renderer class
     */
    public function pushMarkup($parser, $renderer)
    {
        $this->_markupStack->push(
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
     * @desc Retrieve internal data according to current language,
     * assuming default language if the current language is not found.
     */
    protected function getData()
    {
        if( null === $this->_data ) {
            $locale = LocaleSession::getInstance();

            try {
                $data = new \Zend_Config_Xml(
                    $this->_xmlPath,
                    $locale->getCurrentLocale(),
                    array('allowModifications' => true)
                );
            } catch( \Zend_Config_Exception $e ) {
                $data = new \Zend_Config_Xml(
                    $this->_xmlPath,
                    $locale->getDefaultLocale(),
                    array('allowModifications' => true)
                );
            }

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
