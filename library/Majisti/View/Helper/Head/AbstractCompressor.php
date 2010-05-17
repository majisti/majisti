<?php

namespace Majisti\View\Helper\Head;

use \Majisti\Util\Minifying as Minifying;

/**
 * @desc The abstract minifier provides minify abstraction for
 * concrete minifiers.
 *
 * @author Majisti
 */
abstract class AbstractCompressor implements ICompressor
{
    /**
     * @var bool
     */
    protected $_bundlingEnabled;

    /**
     * @var Compression\ICompressor
     */
    protected $_compressor;

    /**
     * FIXME: wrong path!
     * @var <type>
     */
    protected $_stylesheetsPath =
        '/home/ratius/www/majisti/tests/library/Majisti/View/Helper/_files';

    protected $_cacheFile = '.cached-stylesheets';

    protected $_remappedUris = array();

    /**
     * @desc Returns whether bundling is enabled. By default, when this
     * function is called without first using {@link setBundlingEnabled()}
     * (therefore lazily called) it will return true if the current
     * application environment is set to production or staging (as
     * defined in the APPLICATION_ENVIRONMENT constant.
     *
     * @return bool Whether bundling is enabled or not
     */
    public function isBundlingEnabled()
    {
        /*
         * production and staging are enabled by default when function it is
         * lazily called
         */
        if( null == $this->_bundlingEnabled ) {
            $this->_bundlingEnabled = defined('APPLICATION_ENVIRONMENT')
                    && 'production' === APPLICATION_ENVIRONMENT
                    || 'staging' === APPLICATION_ENVIRONMENT;
        }

        return $this->_bundlingEnabled;
    }

    public function isCached()
    {
        return false;
    }

    /**
     * @desc Enables bundling for appended stylesheets when
     * {@link AbstractCompressor::bundle()} is called.
     *
     * @param bool $flag True will enable bundling.
     */
    public function setBundlingEnabled($flag = true)
    {
        $this->_bundlingEnabled = (bool) $flag;
    }

    protected function flushCache()
    {
        @unlink($this->getCacheFilePath());
    }

    public function getCacheFilePath()
    {
        return $this->_stylesheetsPath . '/' . $this->_cacheFile;
    }

    protected function cache($path)
    {
        $cacheFile = $this->getCacheFilePath();

        if( !file_exists($cacheFile) ) {
            touch($cacheFile);
        }

        $handle = fopen($cacheFile, 'a');
        fwrite($handle, $path . ' ' . filectime($path) . PHP_EOL);
        fclose($handle);
    }

    public function compress($header, $path, $url)
    {
        $this->bundle($header, $path, $url);
        $this->minify($header, $path, $url);
    }

    /**
     * @desc Remaps a given uri found within the headlink while minifying.
     * The file must be accessible through the path provided, meaning that
     * internet uris cannot be mapped.<br><br>
     *
     * Ex: You have http://static.mydomain.com/foo.css in the headlink
     * and you can access it with /path/to/foo.css. When minifying to
     * /mydomain/styles/all.css, the css will be added to the generated file.
     * Since the path is accessible, it makes use of the internal caching
     * which will not overide the master stylesheet unless foo.css was changed.
     *
     * @param string $uri The internal uri, accessible with a path
     * @param string $path The path to the stylesheet
     */

    public function getCompressor()
    {
        if ( null === $this->_compressor ) {
            $this->_compressor = new Minifying\Yui();
        }

        return $this->_compressor;
    }

    public function setCompressor(Compression\ICompressor $compressor)
    {
        $this->_compressor = $compressor;
    }

    public function uriRemap($uri, $path)
    {
        $this->_remappedUris[$uri] = $path;
    }

    public function getRemappedUris()
    {
        return $this->_remappedUris;
    }
}
