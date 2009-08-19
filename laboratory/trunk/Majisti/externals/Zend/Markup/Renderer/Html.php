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
 * @version    $Id: Html.php 12767 2008-11-22 13:46:28Z kokx $
 */

/**
 * @see Zend_Markup_Renderer_Abstract
 */
require_once 'Zend/Markup/Renderer/Abstract.php';

/**
 * HTML renderer
 *
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Markup_Renderer_Html extends Zend_Markup_Renderer_Abstract
{

    /**
     * Constructor
     *
     * @param array $options
     *
     * @return void
     */
    public function __construct(array $options = array())
    {
        $options['tags'] = array(
            'b' => array(
                'type'    => Zend_Markup::REPLACE,
                'options' => array(
                	'start' => '<strong>',
                	'end'   => '</strong>',
                    'allow' => array('s', 'i', 'u')
                )
            ),
            'i' => array(
                'type'    => Zend_Markup::REPLACE,
                'options' => array(
                	'start' => '<em>',
                	'end'   => '</em>',
                    'allow' => array('b', 's', 'u')
                )
            ),
            'u' => array(
                'type'    => Zend_Markup::REPLACE,
            	'options' => array(
                	'start' => '<span style="text-decoration: underline;">',
                	'end'   => '</span>',
                    'allow' => array('b', 'i', 's')
                )
            ),
            's' => array(
                'type'    => Zend_Markup::REPLACE,
                'options' => array(
                	'start' => '<strike>',
                	'end'   => '</strike>',
                    'allow' => array('b', 'i', 'u')
                )
            ),
            'hr' => array(
                'type'    => Zend_Markup::REPLACE_SINGLE,
                'options' => array(
                	'replace' => '<hr />'
                )
            ),
            'code' => array(
                'type'    => Zend_Markup::CALLBACK,
                'options' => array(
                	'callback'  => array($this, 'htmlCode'),
                	'filtering' => false,
                    'allow'     => array()
                )
            )
        );

        parent::__construct($options);
    }

    /**
     * Filter the text
     *
     * @param string $text
     *
     * @return string
     */
    protected function _filter($text)
    {
        return nl2br(htmlspecialchars($text));
    }

    /**
     * Code function
     *
     * @param string $text
     * @param array $attributes
     *
     * @return string
     */
    public function htmlCode($text, $attributes)
    {
        if (isset($attributes['lang'])) {
            $lang = $attributes['lang'];
        } elseif (isset($attributes['code'])) {
            $lang = $attributes['code'];
        } else {
            $lang = 'php';
        }

        switch ($lang) {
            case 'php':
                return highlight_string($text, true);
                break;
            default:
                return '<pre>' . htmlspecialchars($text) . '</pre>';
                break;
        }
    }
}