<?php

namespace Majisti\View\Helper\Head;

/**
 * @desc This StylesheetCompressor can bundle and minify all currently appended
 * stylesheets from a given HeadLink to a new file using url versioning
 * for browser cache flushing. The merged file will not be generated until
 * at least one of the given stylsheets gets modified.
 *
 * @author Majisti
 */
class StylesheetCompressor extends AbstractCompressor
{
    /**
     * @desc Bundles the currently appended stylesheets into a new stylesheet
     * provided with the path. Currently only works on screen media types.
     *
     * @param \Zend_View_Helper_Headlink $header The header object
     * @param string $path The path to the bundled file
     * @param string $url The url to the bundled file
     *
     * @throws Exception If given header is not an headlink or if any
     * given css path is not valid
     */
    public function bundle($header, $path, $url)
    {
        if( !$this->isBundlingEnabled() || $this->isCached() ) {
            return $url;
        }

        /* content */
        $content = '';
        $links   = array();

        $callback = function($filepath) use (&$content) {
            $content .= file_get_contents($filepath);
        };

        $this->parseHeader($header, $path, $url, $callback);

        if( empty($content) ) {
            throw new Exception('No content to bundle');
        }

        /* store bundled css content */
        file_put_contents($path, $content);

        /* append version */
        $url .= '?v=' . filemtime($path);

        /* remove merged stylesheets from HeadLink and push the merged one */
        $header->exchangeArray($links);
        $header->appendStylesheet($url);

        return $url;
    }

    protected function parseHeader($header, $path, $url, $callback)
    {
        if( !($header instanceof \Zend_View_Helper_HeadLink) ) {
            throw new Exception("Given header must be an instance of
                    Zend_View_Helper_HeadLink, " . get_class($header)
                    . " given");
        }

        foreach ($header as $head) {
            if( !('stylesheet' === $head->rel && 'screen' === $head->media) ) {
                $links[] = $head;
            } else {
                /* unversionize href */
                $filepath = preg_replace('/\?.*/', '', $head->href);
                if( \Zend_Uri::check($filepath) ) {
                    $remappedUris = $this->getRemappedUris();
                    if( array_key_exists($filepath, $remappedUris) ) {
                        $filepath = $remappedUris[$filepath];
                    } else {
                        $links[] = $head;
                        continue;
                    }
                }

                if( !file_exists($filepath) ) {
                    $filepath = rtrim($_SERVER['DOCUMENT_ROOT'], '/')
                              . '/' . ltrim($filepath, '/');
                }

                if( !file_exists($filepath) ) {
                    throw new Exception("File {$filepath} does not exist");
                }

                $callback($filepath);

                if( $this->isCacheEnabled() ) {
                    $this->addToCache($filepath);
                }
            }
        }
    }

    public function minify($header, $path, $url)
    {
        if( !$this->isMinifyingEnabled() || $this->isCached() ) {
            return $url;
        }

        $minifier = $this->getMinifier();
        $callback = function($filepath) use($minifier) {
            file_put_contents($filepath . '.min',
                $minifier->minifyCss(file_get_contents($filepath)));
        };

        $this->parseHeader($header, $path, $url, $callback);
    }
}
