<?php

namespace Majisti\View\Helper\Head;

/**
 * @desc Minify tests were taking way too long to complete, so a mock object
 * is being used instead of the real deal.
 *
 * @author Majisti
 */
class MinifierMock extends \Majisti\Util\Minifying\AbstractMinifier
{
    /*
     * the behaviour of the minify functions varies according to in wich state
     * the mock object is.
     *
     * ALL_STATE represents the case in wich all of the files (core, file1 and
     * file2) are minified.
     *
     * ALL_INC_INLINE_STATE represents the state in wich core, file1, file2 and
     * inline content have been minified.
     *
     * CORE_AND_FILE1_STATE represents the state in wich only core and file1
     * have been minified. Will usually be follow up by an ALL_STATE call in
     * the unit tests.
     */
    const ALL_STATE = "all";
    const ALL_INC_INLINE_STATE = "allInline";
    const CORE_AND_FILE1_STATE = "coreFile1";

    /* static state that can be modified statically */
    static protected $state;

    /**
     * @desc Mock function that returns what content the files contain depending
     * on the current state.
     *
     * @param $content anything, not taken in account
     * @param $options anything, not taken in account
     * @return string the expected file content depend on current state
     */
    public function minifyCss($content, $options = array())
    {
        switch( $this->getState() ) {
            case self::ALL_STATE:
                $css = ".core{color:red;}.theme1{color:green;}.theme2{color:blue;}";
                break;
            case self::ALL_INC_INLINE_STATE:
                $css = ".core{color:red;}.theme1{color:green;}.theme2{color:blue;}.inline{color:white;}";
                break;
            case self::CORE_AND_FILE1_STATE:
                $css = ".core{color:red;}.theme1{color:green;}";
                break;
            default:
                $css = null;
        }

        return $css;
    }

    /**
     * @desc Mock function that returns what content the files should
     * contain depending on the current state.
     *
     * @param $content anything, not taken in account
     * @param $options anything, not taken in account
     * @return string the expected file content depend on current state
     */
    public function minifyJs($content, $options = array())
    {
        switch( $this->getState() ) {
            case self::ALL_STATE:
                $js = 'function foo(){window.print("foo")}function bar(){window.print("bar")}function baz(){window.print("baz")};';
                break;
            case self::ALL_INC_INLINE_STATE:
                $js = 'function foo(){window.print("foo")}function bar(){window.print("bar")}function baz(){window.print("baz")}function helloWorld(){window.print("Hello World!")};';
                break;
            case self::CORE_AND_FILE1_STATE:
                $js = 'function foo(){window.print("foo")}function bar(){window.print("bar")};';
                break;
            default:
                $js = null;
        }

        return $js;
    }

    /**
     * @desc setter that will change the current state of the mock object.
     * state options are 'all', 'allInline' and 'coreFile1'.
     *
     * @param string $state
     */
    public static function setState($state)
    {
        self::$state = $state;
    }

    /**
     * @desc returns the current mock object's state.
     *
     * @return string the current state
     */
    public function getState()
    {
        return self::$state;
    }
}
