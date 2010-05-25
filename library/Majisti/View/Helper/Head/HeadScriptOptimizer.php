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
class HeadScriptOptimizer extends AbstractOptimizer
{
    /**
     * @desc Returns the default options
     * @return array The default options
     */
    public function getDefaultOptions()
    {
        if( null === $this->_defaultOptions ) {
            $defaultOptions = parent::getDefaultOptions();
            $this->_defaultOptions = array_merge(
                $defaultOptions,
                array(
                    'cacheFile' => '.scripts-cache',
                    'path'      => $defaultOptions['path'] . '/scripts'
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
        return isset($head->src)
                ? $head->src
                : $head->attributes['src'];
    }

    /**
     * @desc Appends data to the header
     * @param string $data The data to append
     */
    protected function appendToHeader($data)
    {
        $this->getHeader()->appendFile((string)$data);
    }

    /**
     * @desc Returns the inline content using the headScript inline content.
     *
     * @return string The inline content
     */
    protected function getInlineContent()
    {
        $content = '';

        foreach( $this->getHeader() as $head ) {
            if( $this->isInlineHead($head) ) {
                $content .= $head->source;
            }
        }
        return $content;
    }

    /**
     * @desc Returns the header object
     * @return \Zend_View_Helper_HeadLink An instance of the headlink header
     */
    public function getHeader()
    {
        return $this->getView()->headScript();
    }

    /**
     * @desc Returns if the given head is a valid javascript.
     * Works only on non conditional script files of type text/javascript.
     *
     * @param stdClass $head The head
     * @return bool True if it is a valid javascript file
     */
    protected function isValidHead($head)
    {
        return 'text/javascript' === $head->type 
            && !isset($head->attributes['conditional'])
            && ( isset($head->attributes['src']) || isset($head->src) )
            && !$this->isInlineHead($head);
    }

    /**
     * @desc Returns if the given head is an inline head
     *
     * @param object $head The head
     *
     * @return True if it is an inline head
     */
    protected function isInlineHead($head)
    {
        return isset($head->source) && null !== $head->source;
    }
}
