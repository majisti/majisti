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
    public function bundle($path, $url)
    {
        if( !$this->isBundlingEnabled() ) {
            return false;
        }

        /* content */
        $content    = '';
        $callback   = null;
        $header     = $this->getHeader();

        if( !$this->isCached() ) {
            $callback = function($filepath) use (&$content) {
                $content .= file_get_contents($filepath);
            };
        }

        $links = $this->parseHeader($header, $callback)->links;

        if( null !== $callback ) {
            if( empty($content) ) {
                throw new Exception('No content to bundle');
            }
            /* store bundled css content */
            file_put_contents($path, $content);

            /* append version */
            $url .= $this->getVersionRequest($path);
        } else {
            $url .= $this->getCachedTimestamp($path);
        }


        /* remove merged stylesheets from HeadLink and push the merged one */
        $header->exchangeArray($links);
        $header->appendStylesheet($url);

        return $url;
    }

    protected function parseHeader($header, $callback = null)
    {
        if( !($header instanceof \Zend_View_Helper_HeadLink) ) {
            throw new Exception("Given header must be an instance of
                    Zend_View_Helper_HeadLink, " . get_class($header)
                    . " given");
        }

        $links = array();
        $urls  = array();

        foreach ($header as $head) {
            if( !('stylesheet' === $head->rel && 'screen' === $head->media) ) {
                $links[] = $head;
            } else {
                /* unversionize href */
                $filepath = preg_replace('/\?.*/', '', $head->href);
                $urls[]   = $filepath;
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
                    $filepath = realpath(rtrim($_SERVER['DOCUMENT_ROOT'], '/')
                              . '/' . ltrim($filepath, '/'));
                }

                if( !file_exists($filepath) ) {
                    throw new Exception("File {$filepath} does not exist");
                }

                if( null !== $callback ) {
                    $callback($filepath);

                    if( $this->isCacheEnabled() ) {
                        $this->addToCache($filepath);
                    }
                }
            }
        }

        $obj = new \stdClass();
        $obj->links = $links;
        $obj->urls  = $urls;

        return $obj;
    }

    public function minify()
    {
        if( !$this->isMinifyingEnabled() ) {
            return false;
        }

        $callback = null;
        $header   = $this->getHeader();

        if( !$this->isCached() ) {
            $minifier = $this->getMinifier();
            $callback = function($filepath) use($minifier) {
                $pathinfo = pathinfo($filepath);
                $ext      = $pathinfo['extension'];

                file_put_contents(rtrim($filepath, $ext) . "min.{$ext}",
                    $minifier->minifyCss(file_get_contents($filepath)));
            };
        }

        $obj = $this->parseHeader($header, $callback);

        foreach( $obj->urls as &$url ) {
            $url = $this->prependMinToExtension($url);
        }

        $header->exchangeArray($obj->links);

        foreach( $obj->urls as $url ) {
            $header->appendStylesheet($url);
        }

        return $obj->urls;
    }
}
