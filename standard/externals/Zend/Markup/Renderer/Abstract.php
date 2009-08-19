<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Abstract.php 12767 2008-11-22 13:46:28Z kokx $
 */

/**
 * @see Zend_Markup
 */
require_once 'Zend/Markup.php';

/**
 * Defines the basic rendering functionality
 *
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Markup_Renderer_Abstract
{

    /**
     * The parser
     *
     * @var Zend_Markup_Parser_Interface
     */
    protected $_parser;

    /**
     * The stack
     *
     * @var array
     */
    protected $_stack = array();

    /**
     * The defined tags
     *
     * @var array
     */
    protected $_tags = array();

    /**
     * Allowence for filtering
     *
     * @var array
     */
    protected $_allowFiltering = array();

    /**
     * The current tag
     *
     * @var array
     */
    protected $_currentTag = array(
    	'tag'        => '',
    	'type'       => Zend_Markup::TYPE_TAG,
    	'name'       => '_ROOT_',
    	'stoppers'   => array(),
    	'attributes' => array()
    );

    /**
     * Allowing of tags
     *
     * @var array|null
     */
    protected $_allow;


    /**
     * Constructor
     *
     * @param array $options
     *
     * @return void
     */
    public function __construct(array $options = array())
    {
        if (isset($options['parser'])) {
            $this->setParser($options['parser']);
        }
        if (isset($options['tags'])) {
            $this->setTags($options['tags']);
        }
    }

    /**
     * Set the parser
     *
     * @param Zend_Markup_Parser_Interface $parser
     *
     * @return Zend_Markup_Renderer_Abstract
     */
    public function setParser(Zend_Markup_Parser_Interface $parser)
    {
        $this->_parser = $parser;

        return $this;
    }

    /**
     * Get the parser
     *
     * @return Zend_Markup_Parser_Interface
     */
    public function getParser()
    {
        return $this->_parser;
    }

    /**
     * Set tags
     *
     * @param array $tags
     *
     * @return Zend_Markup_Renderer_Abstract
     */
    public function setTags(array $tags)
    {
        return $this->clearTags()->addTags($tags);
    }

    /**
     * Clear the tags
     *
     * @return Zend_Markup_Renderer_Abstract
     */
    public function clearTags()
    {
        $this->_tags = array();

        return $this;
    }

    /**
     * Add multiple tags at once
     *
     * @param array $tags
     *
     * @return Zend_Markup_Renderer_Abstract
     */
    public function addTags(array $tags)
    {
        foreach ($tags as $name => $tag) {
            $this->addTag($name, $tag['type'], $tag['options']);
        }

        return $this;
    }

    /**
     * Add a tag
     *
     * @param string $type
     * @param array $options
     *
     * @return Zend_Markup_Renderer_Abstract
     */
    public function addTag($name, $type, array $options)
    {
        if (isset($options['filtering']) && !$options['filtering']) {
            $this->_allowFiltering[$name] = false;
        } else {
            $this->_allowFiltering[$name] = true;
        }

        $this->_tags[$name] = array('type' => $type, 'options' => $options);

        return $this;
    }


    /**
     * Render function
     *
     * @return string
     */
    public function render($value)
    {
        if (!is_string($value) && !is_array($value)) {
            throw new Zend_Markup_Renderer_Exception('The input value should be a string or a token array.');
        }
        if (is_string($value)) {
            if (null === $this->_parser) {
                throw new Zend_Markup_Renderer_Exception('There is no parser defined to parse the string.');
            }

            $value = $this->getParser()->parse($value);
        }

        $this->_stack = $value;

        return $this->_render();
    }

    /**
     * Render a piece of text
     *
     * @param array $stoppers
     *
     * @return string
     */
    protected function _render(array $stoppers = array())
    {
        $text = '';

        while ($tag = array_shift($this->_stack)) {
            if (in_array($tag['tag'], $stoppers)) {
                return $text;
            } else {
                switch ($tag['type']) {
                    case Zend_Markup::TYPE_SINGLE:
                    case Zend_Markup::TYPE_TAG:
                        $text .= $this->_executeTag($tag);
                        break;
                    default:
                        if (!isset($this->_allowFiltering[$this->_currentTag['name']])
                        || $this->_allowFiltering[$this->_currentTag['name']]) {
                            $text .= $this->_filter($tag['tag']);
                        } else {
                            $text .= $tag['tag'];
                        }
                        break;
                }
            }
        }

        return $text;
    }

    /**
     * Try to execute a tag and return it's output
     *
     * @param array $tag Information of the tag
     *
     * @return string
     */
    protected function _executeTag(array $tag)
    {
        if (isset($this->_tags[$tag['name']]) && $this->_isAllowed($tag)) {
            $tagInfo = $this->_tags[$tag['name']];

            switch ($tagInfo['type']) {
                case Zend_Markup::REPLACE:
                    // change the current tag and return the text
                    $oldTag = $this->_currentTag;
                    $this->_currentTag = $tag;

                    // context-awareness for tags
                    $oldAllow = $this->_allow;

                    if (isset($tagInfo['options']['allow']) && is_array($tagInfo['options']['allow'])) {
                        if (null === $this->_allow) {
                            $this->_allow = $tagInfo['options']['allow'];
                        } else {
                            $this->_allow = array_intersect($this->_allow, $tagInfo['options']['allow']);
                        }
                    }

                    $text = $tagInfo['options']['start'] . $this->_render($tag['stoppers']) . $tagInfo['options']['end'];

                    $this->_allow = $oldAllow;

                    $this->_currentTag = $oldTag;
                    return $text;
                    break;
                case Zend_Markup::CALLBACK:
                    // change the current tag
                    $oldTag = $this->_currentTag;
                    $this->_currentTag = $tag;

                    // context-awareness for tags
                    $oldAllow = $this->_allow;

                    if (isset($tagInfo['options']['allow']) && is_array($tagInfo['options']['allow'])) {
                        if (null === $this->_allow) {
                            $this->_allow = $tagInfo['options']['allow'];
                        } else {
                            $this->_allow = array_intersect($this->_allow, $tagInfo['allow']);
                        }
                    }

                    // execute the callback for this tag
                    $text = call_user_func_array($tagInfo['options']['callback'], array(
                        $this->_render($tag['stoppers']),
                        $tag['attributes']
                    ));

                    $this->_allow = $oldAllow;

                    $this->_currentTag = $oldTag;
                    return $text;
                    break;
                case Zend_Markup::REPLACE_SINGLE:
                    return $tagInfo['options']['replace'];
                    break;
            }
        } else {
            // the tag isn't executeable, filter if required and return
            if (!isset($this->_allowFiltering[$this->_currentTag['name']])
            || $this->_allowFiltering[$this->_currentTag['name']]) {
                return $this->_filter($tag['tag']);
            } else {
                return $tag['tag'];
            }
        }
    }

    /**
     * Is this tag allowed
     *
     * @return bool
     */
    protected function _isAllowed(array $tag)
    {
        if (null === $this->_allow) {
            return true;
        }
        if (in_array($tag['name'], $this->_allow)) {
            return true;
        }
        return false;
    }

    /**
     * Filter the text
     *
     * @param string $text
     *
     * @return string
     */
    abstract protected function _filter($text);
}