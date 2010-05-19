<?php

namespace Majisti\View\Helper\Head;

use \Majisti\Util\Minifying as Minifying;

/**
 * @desc The abstract compressor provides compression abstraction for
 * concrete compressors.
 *
 * @author Majisti
 */
abstract class AbstractOptimizer implements IOptimizer
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
     * @var bool If the cache mecanism is enabled
     */
    protected $_cacheEnabled;

    /**
     * @var array The default options
     */
    protected $_defaultOptions;

    /**
     * @var array The remapped uris
     */
    protected $_remappedUris;

    protected $_header;

    public function __construct($header, array $options = array())
    {
        $this->_header = $header;
        $this->setOptions($options);
    }

    public function getHeader()
    {
        return $this->_header;
    }

    public function getDefaultOptions()
    {
        if( null === $this->_defaultOptions ) {
            $this->_defaultOptions = array(
                'stylesheetsPath'   => APPLICATION_PATH . '/../public/styles',
                'cacheFile'         => '.cached-stylesheets',
                'cacheEnabled'      => true
            );
        }

        return $this->_defaultOptions;
    }

    public function setOptions(array $options)
    {
        $options    = array_merge($this->getDefaultOptions(), $options);
        $selector   = new \Majisti\Config\Selector(new \Zend_Config($options));

        $this->_stylesheetsPath = (string) $selector->find('stylesheetsPath');
        $this->_cacheFilePath   = (string) $selector->find('cacheFile');
        $this->_cacheEnabled    = (bool)   $selector->find('cacheEnabled');
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

    public function isOptimizationEnabled()
    {
        return $this->isBundlingEnabled() && $this->isMinifyingEnabled();
    }

    public function setOptimizationEnabled($flag = true)
    {
        $flag = (bool) $flag;

        $this->setBundlingEnabled($flag);
        $this->setMinifyingEnabled($flag);
    }

    protected function isDevelEnvironment($var)
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

    protected function validateCache()
    {
        $cache  = $this->getCache();

        if( empty($cache) ) {
            return false;
        }

        list($valid,) = $this->filterHead();
        $masterUrl = $this->getMasterUrl();
        
        if( 1 === count($valid) ) {
            foreach( $cache as $fileinfo ) {
                if( $masterUrl === $fileinfo['url'] ) {
                    return true;
                }
            }
        }

        if( null !== $masterUrl ) {
            foreach( $cache as $key => $fileinfo ) {
                if( $masterUrl === $fileinfo['url'] ) {
                    unset($cache[$key]);
                    break;
                }
            }
        }

        if( 1 === count($valid) && $masterUrl === $valid[0] ) {
            return false;
        }
        
        if( count($cache) !== count($valid) ) {
            $this->clearCache();
            return false;
        }

        $key = 0;
        foreach( $cache as $fileinfo ) {
            if( $valid[$key++] !== $fileinfo['url'] ) {
                $this->clearCache();
                return false;
            }
        }

        return true;
    }

    abstract protected function getMasterUrl();

    public function isCached()
    {
        if( !($this->isCacheEnabled() && is_file($this->getCacheFilePath())) ) {
            return false;
        }

        return $this->validateCache();

        //if( $this->validateCache() ) {
        //    foreach( $lines as $filepath => $fileinfo ) {
        //        if( (int)$fileinfo['timestamp'] === filemtime($filepath) ) {
        //            return false;
        //        }
        //    }
        //}

        //return false;
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

    protected function getCache()
    {
        if( null === $this->_cache ) {
            $this->_cache = $this->getRealCache();
        }

        return $this->_cache;
    }

    public function clearCache()
    {
        $this->_cache = null;
        @unlink($this->getCacheFilePath());
    }

    protected function addToCache($filepath, $url)
    {
        if( null === $this->_cache ) {
            $this->_cache = array();
        }

        if( $this->isCacheEnabled() ) {
            $this->_cache[$filepath] = array(
                'url'       => $url,
                'timestamp' => filemtime($filepath)
            );
        }
    }

    public function getCachedFilePaths()
    {
        return array_keys($this->_cache);
    }

    protected function getCachedTimestamp($path)
    {
        $filepaths = $this->getCachedFilePaths();

        if( array_key_exists($path, $filepaths) ) {
            return $filepaths[$path]['timestamp'];
        }

        return false;
    }

    public function getCacheFilePath()
    {
        return $this->_stylesheetsPath . '/' . $this->_cacheFilePath;
    }

    public function isCacheEnabled()
    {
        return $this->_cacheEnabled;
    }

    public function setCacheEnabled($flag = true)
    {
        $this->_cacheEnabled = (bool) $flag;
    }

    protected function getRealCache()
    {
        $lines = file($this->getCacheFilePath());

        $cache = array();
        foreach( $lines as $line ) {
            list($path, $url, $timestamp) = explode(' ', $line);
            $cache[$path] = array(
                'url'       => $url,
                'timestamp' => $timestamp
            );
        }

        return $cache;
    }

    protected function cache()
    {
        $cache = $this->getCache();

        if( empty($cache) ) {
            throw new Exception('No cache file found');
        }

        $cacheFile = $this->getCacheFilePath();

        if( !file_exists($cacheFile) ) {
            touch($cacheFile);
        } else if( $cache === $this->getRealCache() ) {
            return;
        }

        $handle = fopen($cacheFile, 'w');
        foreach( $cache as $path => $fileinfo ) {
            fwrite($handle, "{$path} {$fileinfo['url']} {$fileinfo['timestamp']}" . PHP_EOL);
        }
        fclose($handle);
    }

    public function optimize($path, $url)
    {
        if( !$this->isOptimizationEnabled() ) {
            return false;
        }

        if( $url = $this->bundle($path, $url) ) {
            /* keep last uris */
            $uris = $this->getRemappedUris();
            $this->clearUriRemaps();
            $this->uriRemap($url, $path);

            $urls = $this->minify($path, $url);
            $url = reset($urls);

            $this->setUriRemaps($uris);

//            if( is_file($path) ) {
//                unlink($path);
//            }
        }

        return $url;
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

            Minifying\Yui::$jarFile = MAJISTI_ROOT .
                '/../externals/yuicompressor-2.4.2.jar';
            Minifying\Yui::$tempDir = '/tmp';
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

    public function setUriRemaps(array $uriRemaps)
    {
        foreach( $uriRemaps as $uri => $path ) {
            $this->uriRemap($uri, $path);
        }
    }

    protected function getVersionRequest($filepath)
    {
        return '?v=' . filemtime($filepath);
    }

    protected function unversionizeRequest($str)
    {
        return preg_replace('/\?.*/', '', $str);
    }

    protected function prependMinToExtension($filepath)
    {
        $pathinfo = pathinfo($filepath);
        $ext      = $pathinfo['extension'];

        return rtrim($filepath, $ext) . "min.{$ext}";
    }
}
