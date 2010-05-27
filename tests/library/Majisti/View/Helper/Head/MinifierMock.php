<?php

namespace Majisti\Util\Minifying;

class MinifierMock implements IMinifier
{
    const ALL_STATE = "all";
    const ALL_INC_INLINE_STATE = "allInline";
    const CORE_AND_FILE1_STATE = "coreFile1";

    protected $state;

    public function minifyCss($content, $options = array())
    {
        switch( $this->getState() ) {
            case ALL_STATE:
                $css = ".core{color:red;}.theme1{color:green;}.theme2{color:blue;}";
                break;
            case ALL_INC_INLINE_STATE:
                $css = ".core{color:red;}.theme1{color:green;}.theme2{color:blue;}.inline{color:white;}";
                break;
            case CORE_AND_FILE1_STATE:
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
            case ALL_STATE:
                $js = 'function foo(){window.print("foo")}function bar(){window.print("bar")}function baz(){window.print("baz")}';
                break;
            case ALL_INC_INLINE_STATE:
                $js = 'function foo(){window.print("foo")}function bar(){window.print("bar")}function baz(){window.print("baz")}function helloWorld(){window.print("Hello World!")}';
                break;
            case CORE_AND_FILE1_STATE:
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
