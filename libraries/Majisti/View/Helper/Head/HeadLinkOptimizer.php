<?php

namespace Majisti\View\Helper\Head;

/**
 * @desc This HeadLinkOptimizer can bundle and minify all currently appended
 * stylesheets from a given HeadLink to a new file using url versioning
 * for browser cache flushing. The merged file will not be generated until
 * at least one of the given stylesheets gets modified.
 *
 * @author Majisti
 */
class HeadLinkOptimizer extends AbstractOptimizer
{
    /**
     * @var string
     */
    protected $_inlineContent;

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function getDefaultOptions()
    {
        if( null === $this->_defaultOptions ) {
            $defaultOptions = parent::getDefaultOptions();
            $this->_defaultOptions = array_merge(
                $defaultOptions,
                array(
                    'cacheFile' => '.stylesheets-cache',
                )
            );
        }

        return $this->_defaultOptions;
    }

    /**
     * @desc Returns the head attribute needed for getting the url.
     * @param stdClass $head The head stdClass
     *
     * @return string The attribute
     */
    protected function getAttr($head)
    {
        return $head->href;
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    protected function appendToHeader($data)
    {
        $this->getHeader()->appendStylesheet((string)$data);
    }

    /**
     * @desc Returns the inline content using the headStyle header.
     *
     * Note: The headStyle object will be truncated after the initial
     * content fetch. The function will return the same content everytime
     * it is called afterwards.
     *
     * @return string The inline content
     */
    protected function getInlineContent()
    {
        if ( null === $this->_inlineContent ) {
            $headstyle = $this->getView()->headStyle();

            $content = '';

            foreach( $headstyle as $item ) {
                $content .= $item->content;
            }

            if( !empty($content) ) {
                $content = PHP_EOL . $content;
            }

            $this->_inlineContent = $content;
            $headstyle->exchangeArray(array());
        }

        return $this->_inlineContent;
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    public function getHeader()
    {
        return $this->getView()->headLink();
    }

    /**
     * @desc Returns if the given head is a valid stylesheet. Currently
     * only screen media types supported without any browser conditional.
     *
     * @param stdClass $head The head
     * @return bool True if it is a valid stylesheet
     */
    protected function isValidHead($head)
    {
        return 'stylesheet' === $head->rel  && !$head->conditionalStylesheet &&
               'text/css'   === $head->type && isset($head->media) &&
               'screen'     === $head->media;
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    protected function isInlineHead($head)
    {
        return false;
    }

    /*
     * (non-phpDoc)
     * @see Inherited documentation.
     */
    protected function getContentToBundle($filepath)
    {
        $content = file_get_contents($filepath);

        $concreteDir = dirname($filepath);
        $masterDir   = dirname($this->_masterPath);

        preg_match('/(' . preg_quote($masterDir, '/') . ')(.*)/',
                        $concreteDir, $matches);

        if( !empty($matches) ) {
            $subDir = ltrim($matches[2], '/');
            $content = preg_replace('/url\(\'(.*)\'\)/', "url('" . $subDir . '/\1' . "')", $content);
        }

        return $content;
    }
}
