<?php

namespace Majisti\View\Helper\Head;

use \Majisti\Util\Minifying as Minifying;

/**
 * @desc The abstract optimizer provides bundling and minifying abstraction for
 * concrete optimizers. By default, it uses a cache system for caching the
 * bundled or minified files. Also, it uses the Majisti\Util\Minifying\Yui
 * compressor by default. That means that java needs to be on the server's path.
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
     * @var string The directory that hold the files to optimize
     */
    protected $_path;

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
     * @var bool If the currently validated cache is valid ot nor 
     */
    protected $_cacheValid;

    /**
     * @var bool If the cache was already validated or not
     */
    protected $_cacheValidated;

    /**
     * @var array The default options
     */
    protected $_defaultOptions;

    /**
     * @var array The remapped uris
     */
    protected $_remappedUris;

    /**
     * @var object An instance of \Zend_View_Helper_Head*
     */
    protected $_header;

    /**
     * @desc Constructs the optimizer by using an head view helper,
     * such as HeadLink or HeadScript with a sets of options that will
     * override any default options
     *
     * @param object $header The head* view helper.
     * @param array $options The options
     */
    public function __construct($header, array $options = array())
    {
        $this->_header = $header;
        $this->setOptions($options);
    }

    /**
     * @desc Returns the header object
     * @return object An instance of \Zend_View_Helper_Head*
     */
    public function getHeader()
    {
        return $this->_header;
    }

    /**
     * @desc Returns the default options
     * @return array The default options
     */
    public function getDefaultOptions()
    {
        if( null === $this->_defaultOptions ) {
            $this->_defaultOptions = array(
                'path'              => APPLICATION_PATH . '/../public',
                'cacheFile'         => '.cache',
                'cacheEnabled'      => true
            );
        }

        return $this->_defaultOptions;
    }

    /**
     * @desc Sets the options for this optimizer, overriding the default ones.
     * @param array $options The options
     */
    public function setOptions(array $options)
    {
        $options    = array_merge($this->getDefaultOptions(), $options);
        $selector   = new \Majisti\Config\Selector(new \Zend_Config($options));

        /* options to override */
        $this->_path            = (string) $selector->find('path');
        $this->_cacheFilePath   = (string) $selector->find('cacheFile');
        $this->_cacheEnabled    = (bool)   $selector->find('cacheEnabled');
        $this->_remappedUris    = $selector->find('remappedUris', array());

        /* instanciate a minifier if one is provided */
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
            $this->_bundlingEnabled = $this->isDevelEnvironment();
        }

        return $this->_bundlingEnabled;
    }

    /**
     * @desc Returns whether bundling and minifying are enabled.
     * By default, when this function is called without
     * first using {@link setOptimizationEnabled()} (therefore
     * lazily called) it will return true if the current application
     * environment is set to production or staging (as defined in
     * the APPLICATION_ENVIRONMENT constant.
     *
     * @return bool Whether bundling and minifying are both enabled or not
     */
    public function isOptimizationEnabled()
    {
        return $this->isBundlingEnabled() && $this->isMinifyingEnabled();
    }

    /**
     * @desc Returns whether the cache was already valided or not.
     *
     * @return bool True is the cache was already validated
     */
    protected function isCacheValidated()
    {
        return $this->_cacheValidated;
    }

    /**
     * @desc Returns true if the current application is running
     * in development (devel or testing). If it is running in
     * staging or production, it returns false.
     *
     * @param string $var
     * @return bool True if the application is running in development env
     */
    protected function isDevelEnvironment()
    {
        /*
         * production and staging are enabled by default
         */
        return defined('APPLICATION_ENVIRONMENT')
                && 'production' === APPLICATION_ENVIRONMENT
                || 'staging' === APPLICATION_ENVIRONMENT;
    }

    /**
     * @desc Returns whether minifying is enabled.
     * By default, when this function is called without
     * first using {@link setMinifyingEnabled()} (therefore
     * lazily called) it will return true if the current application
     * environment is set to production or staging (as defined in
     * the APPLICATION_ENVIRONMENT constant.
     *
     * @return bool Whether minifying is enabled or not
     */
    public function isMinifyingEnabled()
    {
        if( null === $this->_minifyingEnabled ) {
            $this->_minifyingEnabled = $this->isDevelEnvironment();
        }

        return $this->_minifyingEnabled;
    }

    /**
     * @desc Validates the cache returning true if it is valid, false if not.
     * If the cache is invalid, it will be cleared before returning false.
     *
     * A invalid cache is as such:
     *
     * - cache empty
     *
     * @return True if the cache is valid, false (after cache is flushed)
     * otherwise.
     */
    protected function validateCache()
    {
        $cache = $this->getCache();
        $this->setCacheValidated();

        /* no cache */
        if( empty($cache) ) {
            return false;
        }

        list($validUrls,) = $this->filterHead();
        $masterUrl        = $this->getMasterUrl();

        /*
         * valid cache if the masterUrl is the only
         * valid url which is contained in the cache
         */
        if( 1 === count($validUrls) ) {
            foreach( $cache as $fileinfo ) {
                if( $masterUrl === $fileinfo['url'] ) {
                    return true;
                }
            }
        }

        /*
         * invalid cache if the masterUrl is the only valid url
         */
        if( $masterUrl === reset($validUrls) ) {
            return false;
        }

        /* remove master url from temp cache */
        foreach( $cache as $key => $fileinfo ) {
            if( $masterUrl === $fileinfo['url'] ) {
                unset($cache[$key]);
                break;
            }
        }

        /* invalid cache if the file count is not the same */
        if( count($cache) !== count($validUrls) ) {
            $this->clearCache();
            return false;
        }

        /*
         * invalid cache if the file modification
         * time differs for at least one file contained in the cache
         */
        $key = 0;
        foreach( $cache as $path => $fileinfo ) {
            if( !($validUrls[$key++] === $fileinfo['url'] &&
                    filemtime($path) === (int)$fileinfo['timestamp']))
            {
                $this->clearCache();
                return false;
            }
        }

        return true;
    }

    /**
     * @desc Returns whether all the files are correctly cached or not.
     * If the case where a single file has an outdated timestamp, the cache will
     * be automatically cleared before returning false.
     *
     * @return bool True if all the files are correctly cached, false (after
     * cache flushed) otherwise.
     */
    public function isCached()
    {
        if( !($this->isCacheEnabled() && is_file($this->getCacheFilePath())) ) {
            return false;
        }

        if( $this->isCacheValidated() ) {
            return $this->_cacheValid;
        }

        $this->_cacheValid = $this->validateCache();
        return $this->_cacheValid;
    }

    /**
     * @desc Returns the cache, note that the real cache is retrieved
     * only once from the file, which is used only in an object oriented way
     * after that so that no further disk read is done.
     * 
     * @return array The cache 
     */
    public function getCache()
    {
        if( null === $this->_cache ) {
            $this->_cache = $this->getRealCache();
        }

        return $this->_cache;
    }

    /**
     * @desc Clears the cache and removes the cached file
     */
    public function clearCache()
    {
        if( $this->isCacheEnabled() ) {
            $this->_cache = null;
            @unlink($this->getCacheFilePath());
        }
    }

    /**
     * @desc Adds a filepath and url to the cache. The timestamp will
     * be retrived automatically from the filepath and therefore
     * the filepath must exists else this function will behave unexpectedly.
     * Note that the cache file does not get written, see {@link cache()}
     * for that
     *
     * @param string $filepath The filepath
     * @param string $url The url
     *
     * @see AbstractOptimizer::cache()
     */
    protected function addToCache($filepath, $url)
    {
        if( $this->isCacheEnabled() ) {
            $this->_cache[$filepath] = array(
                'url'       => $url,
                'timestamp' => filemtime($filepath)
            );
        }
    }

    /**
     * @desc Returns an array of the file paths contained in the cache
     * @return array The cached file paths
     */
    public function getCachedFilePaths()
    {
        return array_keys($this->getCache());
    }

    /**
     * @desc Returns a cached timestamp with the path provided, if it is
     * existant in the cache.
     *
     * @param string $path The path
     * @return int|false The timestamp or false if the path was not contained
     * in the cache.
     */
    protected function getCachedTimestamp($path)
    {
        $filepaths = $this->getCachedFilePaths();

        if( array_key_exists($path, $filepaths) ) {
            return (int)$filepaths[$path]['timestamp'];
        }

        return false;
    }

    /**
     * @desc Returns The cache file's path
     * @return string the cache filepath
     */
    public function getCacheFilePath()
    {
        return $this->_path . DIRECTORY_SEPARATOR . $this->_cacheFilePath;
    }

    /**
     * @desc Returns whether the cache is enabled or not
     * @return bools If the cache is enabled.
     */
    public function isCacheEnabled()
    {
        return $this->_cacheEnabled;
    }

    /**
     * @desc Enables or disables the cache
     * @param bool $flag [opt; def=true] The enabled flag
     */
    public function setCacheEnabled($flag = true)
    {
        $this->_cacheEnabled = (bool) $flag;
    }

    /**
     * @desc Flags the cache as validated or not
     * @param bool $flag [opt; def=true] The validated flag
     */
    protected function setCacheValidated($flag = true)
    {
        $this->_cacheValidated = (bool) $flag;
    }

    /**
     * @desc Enables or disables bundling
     * @param bool $flag [opt; def=true] The enabled flag
     */
    public function setBundlingEnabled($flag = true)
    {
        $this->_bundlingEnabled = (bool) $flag;
    }

    /**
     * @desc Enables or disables minifying
     * @param bool $flag [opt; def=true] The enabled flag
     */
    public function setMinifyingEnabled($flag = true)
    {
        $this->_minifyingEnabled = (bool) $flag;
    }

    /**
     * @desc Enables or disables optimization
     * @param bool $flag [opt; def=true] The enabled flag
     */
    public function setOptimizationEnabled($flag = true)
    {
        $flag = (bool) $flag;

        $this->setBundlingEnabled($flag);
        $this->setMinifyingEnabled($flag);
    }

    /**
     * @desc Returns the real cache, from the cache file. It transforms
     * the file into an array($path => array('url' => $url,
     * 'timestamp' => $timestamp))
     *
     * @return array The real cache
     */
    protected function getRealCache()
    {
        $lines = file($this->getCacheFilePath());

        /* transform into understandable array */
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

    /**
     * @desc Flushes the object cache into the cache file, located at
     * the cache file's path only if the internal cache had difference
     * with the real cache. This occurs a disk read but may save a disk write.
     */
    protected function cache()
    {
        $cache     = $this->getCache();
        $cacheFile = $this->getCacheFilePath();

        /* create the cache file */
        if( !file_exists($cacheFile) ) {
            touch($cacheFile);
        }

        /* cache did not change, do not cache again */
        if( $cache === $this->getRealCache() ) {
            return;
        }

        /* write to file, overwriting everything in it */
        $handle = fopen($cacheFile, 'w');
        foreach( $cache as $path => $fileinfo ) {
            fwrite($handle, "{$path} {$fileinfo['url']} {$fileinfo['timestamp']}" . PHP_EOL);
        }
        fclose($handle);
    }

    /**
     * @desc Optimizes the current head, bundling everything in a master file
     * provided by path and url. Afterwards, the master file gets minified
     * with the .min prepended to its extension.
     *
     * @param string $path The master file path
     * @param string $url  The master file url
     *
     * @return string The master file url with ?v=$timestamp appended to it
     * where $timestamp is the generated master file's modification time.
     */
    public function optimize($path, $url)
    {
        if( !$this->isOptimizationEnabled() ) {
            return false;
        }

        /* bundle and minify */
        if( $url = $this->bundle($path, $url) ) {
            /*
             * remap the actual master file, so 
             * that minify can find it
             */
            $uris = $this->getRemappedUris();
            $this->clearUriRemaps();
            $this->uriRemap($url, $path);

            /* minify */
            $urls = $this->minify($path, $url);
            $url = reset($urls);

            /* put back user uris */
            $this->setUriRemaps($uris);
        }

        $this->setCacheValidated(false);

        return $url;
    }

    /**
     * @desc Returns the minifier used by this optimizer. By default
     * it makes use of the Yui minifier. Java needs to be on the path.
     *
     * @return \Majisti\Util\Minifying\IMinifier The minifier
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

    /**
     * @desc Sets a minifier for this optimizer
     *
     * @param IMinifier $minifier The minifier
     */
    public function setMinifier(Minifying\IMinifier $minifier)
    {
        $this->_minifier = $minifier;
    }

    /**
     * @desc Remaps a given uri found within the head while bundling or minifying.
     * The file must be accessible through the path provided, meaning that
     * internet uris cannot be mapped.
     *
     * Ex: You have http://static.mydomain.com/foo.css in the headlink
     * and you can access it with /path/to/foo.css. When bundling to
     * /mydomain/styles/all.css, the css will be added to the generated file.
     * Since the path is accessible, it makes use of the internal caching
     * which will not override the master stylesheet unless foo.css was changed.
     *
     * Note that if minify is only used, the generated .min files will be generated
     * alongside the original file. So if you have a file added from a library
     * in the head, it will generate the .min file within the library. This is
     * why remapping uris works best with {@link optimize()} or {@link bundle()}.
     *
     * @param string $uri The internal uri, accessible with a path
     * @param string $path The path to the stylesheet
     */
    public function uriRemap($uri, $path)
    {
        $this->_remappedUris[$uri] = $path;
    }

    /**
     * @desc Remove a mapped uri.
     *
     * @param string $uri The uri
     *
     * @return AbstractOptimizer this
     */
    public function removeUriRemap($uri)
    {
        unset($this->_remappedUris[$uri]);

        return $this;
    }

    /**
     * @desc Clear all the remapped uris.
     *
     * @return AbstractOptimizer this
     */
    public function clearUriRemaps()
    {
        $this->_remappedUris = array();

        return $this;
    }

    /**
     * @desc Returns the remapped uris.
     *
     * @return array The remapped uris
     */
    public function getRemappedUris()
    {
        return $this->_remappedUris;
    }

    /**
     * @desc Sets all the remapped uris at once. The array provided
     * must follot the array($uri => $path) syntax.
     *
     * @param array $uriRemaps The remapped uris
     *
     * @return AbstractOptimizer this
     */
    public function setUriRemaps(array $uriRemaps)
    {
        foreach( $uriRemaps as $uri => $path ) {
            $this->uriRemap($uri, $path);
        }

        return $this;
    }

    /**
     * @desc Returns the version request for a given filepath. The
     * file must exists else this function will behave unexpectedly.
     *
     * Ex: /path/to/foo.css will return ?v={$timestamp}
     * where $timestamp is the file modification time.
     *
     * @return string the version request only, not appended to the filepath.
     */
    protected function getVersionQuery($filepath)
    {
        return '?v=' . filemtime($filepath);
    }

    /**
     * @desc Unversionize a string.
     *
     * Ex: foo.css?v=1234567 will return foo.css
     *
     * @return string The unversionized string
     */
    protected function unversionizeQuery($str)
    {
        return preg_replace('/\?.*/', '', $str);
    }

    /**
     * @desc Prepends .min to an extension file. The filepath must exists
     * on disk else this function will behave unexpectedly
     *
     * Ex: foo.css will return foo.min.css
     *
     * @param string $filepath The filepath
     * @return string The filepath with the prepended .min to its extension
     */
    protected function prependMinToExtension($filepath)
    {
        $pathinfo = pathinfo($filepath);
        $ext      = $pathinfo['extension'];

        return rtrim($filepath, $ext) . "min.{$ext}";
    }

    /**
     * @desc Returns the master file url, used only when bundling.
     *
     * @return string The master url
     */
    abstract protected function getMasterUrl();

    /**
     * @desc Filters the head by returning an array of
     * valid and invalid urls: array($arrayValidUrls, $arrayInvalidUrls).
     *
     * @return array The valid and invalid urls
     */
    abstract protected function filterHead();
}
