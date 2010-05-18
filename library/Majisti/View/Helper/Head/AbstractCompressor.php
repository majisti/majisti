<?php

namespace Majisti\View\Helper\Head;

use \Majisti\Util\Minifying as Minifying;

/**
 * @desc The abstract compressor provides compression abstraction for
 * concrete compressors.
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
     * @var bool
     */
    protected $_minifyingEnabled;

    /**
     * @var Compression\ICompressor
     */
    protected $_minifier;

    /**
     * @var string The directory that hold the stylesheets
     */
    protected $_stylesheetsPath;

    /**
     * @var The cache file path
     */
    protected $_cacheFilePath;

    /**
     * @var array The cached files
     */
    protected $_cache;

    /**
     * @var array The default options
     */
    protected $_defaultOptions;

    /**
     * @var array The remapped uris
     */
    protected $_remappedUris;

    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    public function getDefaultOptions()
    {
        if( null === $this->_defaultOptions ) {
            $this->_defaultOptions = array(
                'stylesheetsPath'   => APPLICATION_URL_STYLES,
                'cacheFile'         => '.cached-stylesheets',
            );
        }

        return $this->_defaultOptions;
    }

    public function setOptions(array $options)
    {
        $options    = array_merge($this->getDefaultOptions(), $options);
        $selector   = new \Majisti\Config\Selector(new \Zend_Config($options));

        $this->_stylesheetsPath = (string)$selector->find('stylesheetsPath');
        $this->_cacheFilePath   = (string)$selector->find('cacheFile');
        $this->_remappedUris    = $selector->find('remappedUris', array());

        $minifier = $selector->find('minifier', false);

        if( is_string($minifier) ) {
            $this->setMinifier(new $minifier());
        } elseif( is_object($minifier) ) {
            $this->setMinifier($minifier);
        }
    }

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
        if( null == $this->_bundlingEnabled ) {
            $this->_bundlingEnabled = $this->isDevelEnvironment(
                $this->_bundlingEnabled);
        }

        return $this->_bundlingEnabled;
    }

    public function isCompressionEnabled()
    {
        return $this->isBundlingEnabled() && $this->isMinifyingEnabled();
    }

    private function isDevelEnvironment($var)
    {
        /*
         * production and staging are enabled by default
         */
        $var = defined('APPLICATION_ENVIRONMENT')
                && 'production' === APPLICATION_ENVIRONMENT
                || 'staging' === APPLICATION_ENVIRONMENT;

        return $var;
    }

    public function isMinifyingEnabled()
    {
        if( null === $this->_minifyingEnabled ) {
            $this->_minifyingEnabled = $this->isDevelEnvironment(
                $this->_minifyingEnabled);
        }

        return $this->_minifyingEnabled;
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

    public function setMinifyingEnabled($flag = true)
    {
        $this->_minifyingEnabled = (bool) $flag;
    }

    protected function clearCache()
    {
        @unlink($this->getCacheFilePath());
    }

    protected function addToCache($filepath)
    {
        $this->_cache[] = $filepath;
        array_unique($this->_cache);
    }

    public function getCachedFilePaths()
    {
        return $this->_cache;
    }

    public function getCacheFilePath()
    {
        return $this->_stylesheetsPath . '/' . $this->_cacheFilePath;
    }

    protected function cache()
    {
        $filePaths = $this->getCachedFilePaths();

        if( empty($filePaths) ) {
            throw new Exception('No cache files found');
        }

        $cacheFile = $this->getCacheFilePath();

        if( !file_exists($cacheFile) ) {
            touch($cacheFile);
        }

        $handle = fopen($cacheFile, 'a');
        foreach( $filePaths as $path ) {
            fwrite($handle, $path . ' ' . filemtime($path) . PHP_EOL);
        }
        fclose($handle);
    }

    public function compress($header, $path, $url)
    {
        $url = $this->bundle($header, $path, $url);

        $uris = $this->getRemappedUris();
        $this->clearUriRemaps();
        $this->uriRemap($url, $path);

        $this->minify($header, $path, $url);

        $this->setUriRemaps($uris);

        unlink($path);
        $this->cache();
    }

    /**
     * @desc Remaps a given uri found within the headlink while minifying.
     * The file must be accessible through the path provided, meaning that
     * internet uris cannot be mapped.
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

    public function getMinifier()
    {
        if ( null === $this->_minifier ) {
            $this->_minifier = new Minifying\Yui();
        }

        return $this->_minifier;
    }

    public function setMinifier(Minifying\IMinifier $minifier)
    {
        $this->_minifier = $minifier;
    }

    public function uriRemap($uri, $path)
    {
        $this->_remappedUris[$uri] = $path;
    }

    public function removeUriRemap($uri)
    {
        unset($this->_remappedUris[$uri]);
    }

    public function clearUriRemaps()
    {
        $this->_remappedUris = array();
    }

    public function getRemappedUris()
    {
        return $this->_remappedUris;
    }
}
