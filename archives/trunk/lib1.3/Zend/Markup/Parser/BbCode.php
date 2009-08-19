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
 * @subpackage Parser
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: BbCode.php 12120 2008-10-25 12:50:23Z kokx $
 */

/**
 * @see Zend_Markup
 */
require_once 'Zend/Markup.php';
/**
 * @see Zend_Markup_Parser_Interface
 */
require_once 'Zend/Markup/Parser/Interface.php';

/**
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Parser
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Markup_Parser_BbCode implements Zend_Markup_Parser_Interface
{

    const TAG_START = '[';
    const TAG_END   = ']';

    /**
     * Tokenized array
     *
     * @var array
     */
    protected $_tokenized = array();

    /**
     * Source to tokenize
     *
     * @var string
     */
    protected $_value = '';

    /**
     * Length of the value
     *
     * @var int
     */
    protected $_valueLen = 0;

    /**
     * Current pointer
     *
     * @var int
     */
    protected $_pointer = 0;

    /**
     * The buffer
     *
     * @var string
     */
    protected $_buffer = '';

    /**
     * Temporary element information
     *
     * @var array
     */
    protected $_temp = array();


    /**
     * Parse a string
     *
     * This should output an array like this:
     *
     * <code>
     * // build from the string '[tag="a" attr=val]value[/tag]'
     * array(
     *     array(
     *         'tag'        => '[tag="a" attr=val]',
     *         'type'       => Zend_Markup::TYPE_TAG,
     *         'name'       => 'tag',
     *         'stoppers'   => array('[/]', '[/tag]'),
     *         'attributes' => array(
     *             'tag'  => 'a',
     *             'attr' => 'val'
     *         )
     *     ),
     *     array(
     *         'tag'   => 'value',
     *         'type'  => Zend_Markup::TYPE_NONE
     *     ),
     *     array(
     *         'tag'        => '[/tag]',
     *         'type'       => Zend_Markup::TYPE_STOPPER,
     *         'name'       => 'tag',
     *         'stoppers'   => array(),
     *         'attributes' => array()
     *     )
     * )
     * </code>
     *
     * @param string $value
     *
     * @return array
     */
    public function parse($value)
    {
        if (!is_string($value)) {
            /**
             * @see Zend_Markup_Parser_Exception
             */
            require_once 'Zend/Markup/Parser/Exception.php';
            throw new Zend_Markup_Parser_Exception('The value should be a string.');
        } elseif (empty($value)) {
            /**
             * @see Zend_Markup_Parser_Exception
             */
            require_once 'Zend/Markup/Parser/Exception.php';
            throw new Zend_Markup_Parser_Exception('The value shouldn\'t be empty.');
        }

        // first make we only have "\n" newlines
        $this->_value = str_replace(array("\r\n", "\r"), "\n", $value);

        $this->_tokenize();

        return $this->_tokenized;
    }

    /**
     * Tokenize a string
     *
     * @param string $value
     *
     * @return void
     */
    protected function _tokenize()
    {
        // initialize variables
        $this->_tokenized = array();
        $this->_valueLen  = strlen($this->_value);
        $this->_pointer   = 0;
        $this->_buffer    = '';
        $this->_temp      = array();

        do {
            $this->_parseTagStart();
        } while ($this->_pointer < $this->_valueLen);

        if (strlen($this->_buffer) > 0) {
            $this->_tokenized[] = array(
                'tag'  => $this->_buffer,
                'type' => Zend_Markup::TYPE_NONE
            );
        }
    }

    /**
     * Parse the start of a tag
     *
     * @return void
     */
    protected function _parseTagStart()
    {
        $start = strpos($this->_value, self::TAG_START, $this->_pointer);

        if ($start === false) {
            $this->_tokenized[] = array(
                'tag'  => $this->_buffer . substr($this->_value, $this->_pointer),
                'type' => Zend_Markup::TYPE_NONE
            );
            $this->_pointer = $this->_valueLen;

            $this->_buffer = '';
            return;
        }

        // add the pre text to the buffer
        if ($start > $this->_pointer) {
            $this->_buffer .= substr($this->_value, $this->_pointer, $start - $this->_pointer);
        }

        $this->_pointer = $start;

        // we have the start of this tag, now we need its name
        $this->_parseTagName();
    }

    /**
     * We have detected the start of a new tag, now we want to get the tagname
     *
     * @return void
     */
    protected function _parseTagName()
    {
        $firstChar = $this->_value[++$this->_pointer];

        // we need the len of the name

        $len = strspn($this->_value, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_', $this->_pointer + 1);

        $name = substr($this->_value, $this->_pointer + 1, $len);

        if ($firstChar == '/') {
            // probably a stopper
            if ($this->_value[$this->_pointer + $len + 1] == self::TAG_END) {
                $this->_tokenized[] = array(
                    'tag'  => $this->_buffer,
                    'type' => Zend_Markup::TYPE_NONE
                );
                $this->_buffer = '';

                $this->_tokenized[] = array(
                    'tag'        => self::TAG_START . '/' . $name . self::TAG_END,
                    'type'       => Zend_Markup::TYPE_STOPPER,
                    'name'       => $name,
                    'stoppers'   => array(),
                    'attributes' => array()
                );
            } else {
                $this->_buffer .= self::TAG_START . '/' . $name . $this->_value[$this->_pointer + $len + 1];
            }

            $this->_pointer += $len + 2;
        } elseif (strspn($firstChar, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_') == 0) {
            // not a tag
            $this->_buffer .= self::TAG_START . $firstChar . $name;
            $this->_pointer += $len + 1;
        } else {
            // generic tag

            $name = $firstChar . $name;

            switch ($this->_value[$this->_pointer + $len + 1]) {
                case self::TAG_END:
                    // the end of the tag
                    $this->_tokenized[] = array(
                        'tag'  => $this->_buffer,
                        'type' => Zend_Markup::TYPE_NONE
                    );
                    $this->_buffer = '';

                    $this->_tokenized[] = array(
                        'tag'      => self::TAG_START . $name . self::TAG_END,
                        'type'     => Zend_Markup::TYPE_TAG,
                        'name'     => $name,
                        'stoppers' => array(
                            self::TAG_START . '/' . $name . self::TAG_END,
                            self::TAG_START . '/' . self::TAG_END
                        ),
                        'attributes' => array()
                    );

                    // and update the pointer
                    $this->_pointer += $len + 2;
                    return;
                    break;
                case '=':
                    $attributes = array();

                    $this->_pointer += $len + 2;

                    $tag = self::TAG_START . $name . '=';

                    // look for the attribute value
                    if (($this->_value[$this->_pointer] == '"')
                    || ($this->_value[$this->_pointer] == "'")) {
                        // go to the end of the string
                        $len = strcspn($this->_value, $this->_value[$this->_pointer], $this->_pointer + 1);

                        $val = substr($this->_value, $this->_pointer + 1, $len);

                        $tag .= $this->_value[$this->_pointer] . $val . $this->_value[$this->_pointer];

                        $attributes[$name] = $val;

                        $this->_pointer += $len + 2;
                    } else {
                        $len = strcspn($this->_value, " \n\t" . self::TAG_END, $this->_pointer);

                        $val = substr($this->_value, $this->_pointer, $len);

                        $attributes[$name] = $val;

                        $tag .= $val;

                        $this->_pointer += $len;
                    }

                    // add the tag to the temporary storage
                    $this->_temp = array(
                        'tag'        => $tag,
                        'type'       => Zend_Markup::TYPE_TAG,
                        'name'       => $name,
                        'stoppers'   => array(
                            self::TAG_START . '/' . $name . self::TAG_END,
                            self::TAG_START . '/' . self::TAG_END
                        ),
                        'attributes' => $attributes
                    );
                    break;
                default:
                    // look if we are dealing with a tag
                    if (ctype_space($this->_value[$this->_pointer + $len + 1])) {
                        $this->_temp = array(
                            'tag'        => self::TAG_START . $name . $this->_value[$this->_pointer + $len + 1],
                            'type'       => Zend_Markup::TYPE_TAG,
                            'name'       => $name,
                            'stoppers'   => array(
                                self::TAG_START . '/' . $name . self::TAG_END,
                                self::TAG_START . '/' . self::TAG_END
                            ),
                            'attributes' => array()
                        );

                        $this->_pointer += $len + 2;
                    } else {
                        // no tag, add to buffer and return
                        $this->_buffer .= self::TAG_START . $name . $this->_value[$this->_pointer + $len + 1];

                        $this->_pointer += $len + 2;
                        return;
                    }
                    break;
            }
            $this->_parseAttributes();
        }
    }

    /**
     * Parse the attributes
     *
     * @return void
     */
    protected function _parseAttributes()
    {
        while ($this->_pointer < $this->_valueLen) {
            $char = $this->_value[$this->_pointer++];

            if (ctype_space($char)) {
                $this->_temp['tag'] .= $char;
            } elseif ($char == self::TAG_END) {
                // we end the tag successfully
                if (!empty($this->_buffer)) {
                    $this->_tokenized[] = array(
                        'tag'  => $this->_buffer,
                        'type' => Zend_Markup::TYPE_NONE
                    );
                    $this->_buffer = '';
                }

                $this->_temp['tag'] .= self::TAG_END;

                $this->_tokenized[] = $this->_temp;

                $this->_temp = array();
                return;
            } elseif (strspn($char, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_') == 0) {
                // not a valid tag
                $this->_buffer .= $this->_temp['tag'];

                $this->_temp = array();
                return;
            } else {
                // we probably have found ourselves an attribute
                $len = strspn($this->_value, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_', $this->_pointer);

                $name = $char . substr($this->_value, $this->_pointer, $len);

                $this->_pointer += $len;

                if ($this->_value[$this->_pointer] == '=') {
                    // get the attribute's value
                    $nextChar = $this->_value[++$this->_pointer];

                    if (($nextChar == '"') || ($nextChar == "'")) {
                        $len = strcspn($this->_value, $nextChar, ++$this->_pointer);

                        $val = substr($this->_value, $this->_pointer, $len);

                        $this->_temp['tag'] .= $name . '=' . $nextChar . $val . $nextChar;

                        $this->_temp['attributes'][$name] = $val;

                        $this->_pointer += $len + 1;
                    } else {
                        $len = strcspn($this->_value, " \n\t" . self::TAG_END, ++$this->_pointer);

                        $val = $nextChar . substr($this->_value, $this->_pointer, $len);

                        $this->_temp['tag'] .= $name . '=' . $val;

                        $this->_temp['attributes'][$name] = $val;

                        $this->_pointer += $len;
                    }
                } else {
                    // unvalid attribute, do not add it
                    $this->_buffer .= $this->_temp['tag'] . $name . $this->_value[$this->_pointer];

                    $this->_pointer++;

                    $this->_temp = array();

                    return;
                }
            }
        }

        $this->_buffer .= $this->_temp['tag'];
    }
}