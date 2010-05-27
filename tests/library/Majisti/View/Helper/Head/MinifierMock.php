<?php

namespace Majisti\View\Helper\Head;

class MinifierMock extends \Majisti\Util\Minifying\AbstractMinifier
{
    const ALL_STATE = "all";
    const ALL_INC_INLINE_STATE = "allInline";
    const CORE_AND_FILE1_STATE = "coreFile1";

    protected $state;

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

    public function minifyJs($content, $options = array())
    {
        switch( $this->getState() ) {
            case self::ALL_STATE:
                $js = 'function foo(){window.print("foo")}function bar(){window.print("bar")}function baz(){window.print("baz")}';
                break;
            case self::ALL_INC_INLINE_STATE:
                $js = 'function foo(){window.print("foo")}function bar(){window.print("bar")}function baz(){window.print("baz")}function helloWorld(){window.print("Hello World!")}';
                break;
            case self::CORE_AND_FILE1_STATE:
                $js = 'function foo(){window.print("foo")}function bar(){window.print("bar")};';
                break;
            default:
                $js = null;
        }

        return $js;
    }

    public static function setState($state)
    {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }
}
