<?php

namespace Majisti\View\Helper\Head;

use \Majisti\Util\Minifying as Minifying;

/**
 * @desc The abstract optimizer provides bundling and minifying abstraction for
 * concrete optimizers. By default, it uses a cache system for caching the
 * bundled or minified files. Also, it uses the Majisti\Util\Minifying\Yui
 * minifier by default. That means that java needs to be on the server's path.
 *
 * @author Majisti
 */
abstract class AbstractOptimizer implements IOptimizer
{
    /**
     * @var array The default options
     */
    protected $_defaultOptions;

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
     * @var string The cache namespace
     */
    protected $_cacheNamespace;

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
     * @var array The remapped uris
     */
    protected $_remappedUris;

    /**
     * @var object An instance of \Zend_View_Helper_Head*
     */
    protected $_view;

    /**
     * @var string The master url
     */
    protected $_masterUrl;

    /**
     * @var bool Whether inline content should be appended to the master
     * bundled file
     */
    protected $_appendInline;

    /**
     * @desc Constructs the optimizer by using the view with a sets of
     * options that will override any default options
     *
     * @param \Zend_View $view The view object
     * @param array $options The options
     */
    public function __construct(\Zend_View $view, array $options = array())
    {
        $this->_view = $view;
        $this->setOptions($options);
    }

    /**
     * @desc Returns the default options
     * @return array The default options
     */
    public function getDefaultOptions()
    {
        if( null === $this->_defaultOptions ) {
            $this->_defaultOptions = array(
                'path'                      => APPLICATION_PUBLIC_PATH,
                'cacheFile'                 => '.cache',
                'cacheEnabled'              => true,
                'appendInline'              => true,
                'remappedUris'              => array(
                    MAJISTI_URL_STYLES      => MAJISTI_PUBLIC_PATH  . '/styles',
                    MAJISTIX_URL_STYLES     => MAJISTIX_PUBLIC_PATH . '/styles',

                    JQUERY_PLUGINS          => MAJISTIX_PUBLIC_PATH . '/jquery/plugins',
                    JQUERY_STYLES           => MAJISTIX_PUBLIC_PATH . '/jquery/styles',
                    JQUERY_THEMES           => MAJISTIX_PUBLIC_PATH . '/jquery/themes',
                ),
            );
        }

        return $this->_defaultOptions;
    }

    public function setDefaultOptions(array $defaultOptions)
    {
        $this->_defaultOptions = $defaultOptions;
    }

    /**
     * @desc Returns the view
     * @return \Zend_View the view
     */
    public function getView()
    {
        return $this->_view;
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
        $this->_path             = (string) $selector->find('path');
        $this->_cacheFilePath    = (string) $selector->find('cacheFile');
        $this->_cacheEnabled     = (bool)   $selector->find('cacheEnabled');
        $this->_appendInline     = (bool)   $selector->find('appendInline');
        $this->_bundlingEnabled  = $selector->find('bundlingEnabled',  null);
        $this->_minifyingEnabled = $selector->find('minifyingEnabled', null);
        $this->_remappedUris     = $selector->find('remappedUris', array(), true);

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
     * @desc Returns whether bundling or minifying are enabled.
     *
     * By default, when this function is called without
     * first using {@link setOptimizationEnabled()} (therefore
     * lazily called) it will return true if the current application
     * environment is set to production or staging (as defined in
     * the APPLICATION_ENVIRONMENT constant.
     *
     * @return bool Whether bundling or minifying are both enabled or not
     */
    public function isOptimizationEnabled()
    {
        return $this->isBundlingEnabled() || $this->isMinifyingEnabled();
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
     * @desc Returns whether inline content should be appended
     * to the master file.
     *
     * @return bool True to append inline content
     */
    public function isAppendInlineContent()
    {
        return $this->_appendInline;
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
     * @return True if the cache is valid, false (after cache is flushed)
     * otherwise.
     */
    protected function validateCache()
    {
        $cache = $this->getCache();
        $this->setCacheValidated();

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
            $this->clearCache($this->getCacheNamespace());
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
                $this->clearCache($this->getCacheNamespace());
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
     * @desc Returns the cache namespace
     *
     * @return string The namespace
     */
    protected function getCacheNamespace()
    {
        return $this->_cacheNamespace;
    }

    /**
     * @desc Clears the cache and removes the cached file(s). Works only
     * when the cache is enabled.
     *
     * @param $namespace [opt; def=*] The cache namespace. A cache namespace
     * is identified by the master's filename provided when optimizing or
     * bundling or according to the cache namespace parameter to the minify
     * function. If no namespace is specified it will assume all possible
     * namespace (wildcard).
     *
     * @return AbstractOptimizer this
     */
    public function clearCache($namespace = '*')
    {
        if( $this->isCacheEnabled() ) {
            $this->_cache = null;

            $filepaths = glob($this->getCacheFilePath() . $namespace);
            foreach( $filepaths as $filepath ) {
                @unlink($filepath);
            }
        }

        return $this;
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
    protected function getCacheFilePath()
    {
        return $this->_path . DIRECTORY_SEPARATOR
                . $this->_cacheFilePath . '_' . $this->getCacheNamespace();
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
     * @desc Sets the cache namepsace
     *
     * @param string $cacheNamespace The cache namespace
     */
    protected function setCacheNamespace($cacheNamespace)
    {
        $this->_cacheNamespace = $cacheNamespace;
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
     * @desc Enables or disables inline content append when bundling.
     * @param bool $flag [opt; def=true] The enabled flag
     */
    public function setAppendInlineContent($flag = true)
    {
        $this->_appendInline = (bool) $flag;
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
     * with the real cache, occuring a disk read but may save a disk write.
     */
    protected function cache()
    {
        if( $this->isCached() ) {
            return;
        }

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
            fwrite($handle, "{$path} {$fileinfo['url']} {$fileinfo['timestamp']}"
                    . PHP_EOL);
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
            if( $urls = $this->minify($this->getCacheNamespace()) ) {
                $url = reset($urls);

                /* put back user uris */
                $this->setUriRemaps($uris);
            }

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
                '/externals/yuicompressor-2.4.2.jar';
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
     * @param string $uri The internal uri, accessible with a path without the
     * base path. E.g: http://foo.com
     * @param string $path The path replaceing the uri. E.g: ~/myLibrary/public
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
     * @desc Returns the master url used when bundling
     * @return string The master url
     */
    protected function getMasterUrl()
    {
        return $this->_masterUrl;
    }

    /**
     * @desc Bundles the currently appended items into a new master
     * file provided with the path.
     *
     * @param \Zend_View_Helper_Headlink $header The header object
     * @param string $path The path to the master bundled file
     * @param string $url The url to the master bundled file
     *
     * @throws Exception If any given path is not valid
     */
    public function bundle($path, $url)
    {
        $this->_masterUrl = $this->unversionizeQuery($url);

        $pathinfo = pathinfo($this->_masterUrl);
        $this->setCacheNamespace($pathinfo['filename']);

        if( !$this->isBundlingEnabled() ) {
            return false;
        }

        $content    = '';
        $callback   = null;
        $header     = $this->getHeader();

        /*
         * apply call back function when there is no cache, the call back
         * is the one that aggregates all the content that will get bundled
         * in the master file
         */
        if( !$this->isCached() ) {
            $callback = function($filepath) use (&$content) {
                $content .= file_get_contents($filepath);
            };
        }

        $invalidHeads = $this->parseHeader($header, $callback)->invalidHeads;

        /* bundle in the master file */
        if( !$this->isCached() ) {
            if( $this->isAppendInlineContent() ) {
                $content .= $this->getInlineContent();
            }

            if( empty($content) ) {
                throw new Exception('No content to bundle');
            }

            /* store bundled css content */
            file_put_contents($path, $content);
        }

        /* append version query */
        $url .= $this->getVersionQuery($path);

        /* remove merged items from the header and push the merged one */
        $header->exchangeArray($invalidHeads);
        $this->appendToHeader($url);

        $this->cache();

        return $url;
    }

    /**
     * @desc Filters the head by returning an array of
     * valid and invalid urls: array($arrayValidUrls, $arrayInvalidUrls).
     *
     * @return array The valid and invalid urls
     */
    protected function filterHead()
    {
        $header      = $this->getHeader();
        $validUrls   = array();
        $invalidUrls = array();

        /* retrieve valid and invalid urls */
        foreach($header as $head) {
            if( $this->isValidHead($head) ) {
                $validUrls[] = $this->unversionizeQuery($this->getAttr($head));
            } else if( !$this->isInlineHead($head) ) {
                $invalidUrls[] = $this->getAttr($head);
            }
        }

        return array($validUrls, $invalidUrls);
    }

    /**
     * @desc Minifies all the valid heads inside an header, prepending
     * the .min extension before their respective extension and generating
     * the file according to their paths.
     */
    public function minify($cacheNamespace)
    {
        if( !$this->isMinifyingEnabled() ) {
            return false;
        }

        if( $cacheNamespace !== $this->getCacheNamespace()) {
            $this->_cache = null;
            $this->setCacheNamespace($cacheNamespace);
        }

        $callback = null;
        $header   = $this->getHeader();

        /*
         * apply callback function when there is no cache, the callback
         * is the one that minifies each valid stylesheet
         */
        if( !$this->isCached() ) {
            $minifier = $this->getMinifier();

            $callback = function($filepath) use ($minifier) {
                $pathinfo = pathinfo($filepath);
                $ext      = $pathinfo['extension'];

                file_put_contents(rtrim($filepath, $ext) .
                        "min.{$ext}",
                    $minifier->minify($ext, file_get_contents($filepath)));
            };
        }

        $obj = $this->parseHeader($header, $callback);

        $header->exchangeArray($obj->invalidHeads);

        /* reappend every minified and versionized stylesheets */
        foreach( $obj->validUrls as $key => $url ) {
            $this->appendToHeader( /* append */
                   $this->prependMinToExtension($url) .
                   $this->getVersionQuery($obj->filepaths[$key]));

            $obj->validUrls[$key] = $this->prependMinToExtension($url)
                    . $this->getVersionQuery($obj->filepaths[$key]);
        }

        $this->cache();

        return $obj->validUrls;
    }

    /**
     * @desc Parses the header object with a given callback. The parsed header
     * will return an object with the invalidHeads that were parsed, valid hrefs
     * along with the filepaths.
     *
     * @param object $header The header object
     * @param function $callback The callback function
     *
     * @return stdClass with invalidHeads, validUrls and filepaths keys.
     *
     * @throws  If any given supported path is not valid in the header
     */
    protected function parseHeader($header, $callback = null)
    {
        $invalidHeads   = array();
        $validUrl       = array();
        $filepaths      = array();

        foreach ($header as $head) {
            /* do not even add back to the header file */
            if( $this->isInlineHead($head) ) {
                continue;
            }

            /* unsupported head, preserve it but do not bundle */
            if( !$this->isValidHead($head) ) {
                $invalidHeads[] = $head;
            } else {
                $url        = $this->unversionizeQuery($this->getAttr($head));
                $validUrl[] = $url;

                if( $result = $this->getRemappedPath($url) ) {
                    if( is_string($result) ) {
                        $url = $result;
                    } else {
                        array_pop($validUrl);
                        $invalidHeads[] = $head;
                        continue;
                    }
                }

                /*
                 * the path can be an url relative to a domain, which consists
                 * of the "base url", it it is a base url, let's try to
                 * prepend the document root to the href and test the file
                 * existance
                 */
                if( !file_exists($url) ) {
                    $url = rtrim($_SERVER['DOCUMENT_ROOT'], '/')
                              . '/' . ltrim($url, '/');
                }

                /*
                 * the href provided cannot be mapped to a real path,
                 * and therefore can't be used in both bundle or minify.
                 */
                if( !file_exists($url) ) {
                    throw new Exception("File {$url} does not exist and could " .
                    "not be found using the provided url remaps: " .
                    implode(' :: ', array_keys($this->getRemappedUris())));
                }

                $url         = realpath($url);
                $filepaths[] = $url;

                /*
                 * when there is no cache (thefore a non null callback),
                 * call the callback and add the href to the cache
                 */
                if( null !== $callback ) {
                    $callback($url);
                    $this->addToCache($url, end($validUrl));
                }
            }
        }

        /* return stdClass object with significant data */
        $obj                = new \stdClass();
        $obj->invalidHeads  = $invalidHeads;
        $obj->validUrls     = $validUrl;
        $obj->filepaths     = $filepaths;

        return $obj;
    }

    /**
     * @desc Checks if the given uri is valid and if it is, check if it was
     * remapped to a path. Supports subdirectories.
     *
     * @return mixed the path if it was remapped, -1 if it was not and false
     * if the given uri was not even valid
     */
    protected function getRemappedPath($uri)
    {
        if( \Zend_Uri::check($uri) ) {
            /*
             * return the base remapped url, subdirectories and the file with
             * a preg_match from the remappedUri
             */
            $remappedUris = $this->getRemappedUris();
            foreach( array_keys($remappedUris) as $remappedUri ) {
                if( preg_match('/(' . preg_quote($remappedUri, '/') . ')(.*)\/(.*\..*)/',
                        $uri, $matches) )
                {
                    return $remappedUris[$matches[1]] . $matches[2] . "/{$matches[3]}";
                }
            }

            return -1;
        }

        return false;
    }

    /**
     * @desc Returns the version request for a given filepath. The
     * file must exists else this function will behave unexpectedly.
     *
     * Ex: /path/to/foo.ext will return ?v={$timestamp}
     * where $timestamp is the file modification time.
     *
     * @return string the version request only, not appended to the filepath.
     */
    public function getVersionQuery($filepath)
    {
        return '?v=' . filemtime($filepath);
    }

    /**
     * @desc Unversionize a string.
     *
     * Ex: foo.ext?v=1234567 will return foo.ext
     *
     * @return string The unversionized string
     */
    public function unversionizeQuery($str)
    {
        return preg_replace('/\?.*/', '', $str);
    }

    /**
     * @desc Prepends .min to an extension file. The filepath must exists
     * on disk else this function will behave unexpectedly
     *
     * Ex: foo.ext will return foo.min.ext
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
     * @desc Returns the head attribute needed for getting the url.
     * @param stdClass $head The head stdClass
     *
     * @return string The attribute
     */
    abstract protected function getAttr($head);

    /**
     * @desc Returns if the given head is a valid one.
     *
     * @param stdClass $head The head
     *
     * @return bool True if it is a valid head
     */
    abstract protected function isValidHead($head);

    /**
     * @desc Returns if the given head is an inline head
     *
     * @param object $head The head
     *
     * @return True if it is an inline head
     */
    abstract protected function isInlineHead($head);

    /**
     * @desc Appends data to the header
     * @param string $data The data to append
     */
    abstract protected function appendToHeader($str);

    /**
     * @desc Appends data to the header
     * @param string $data The data to append
     */
    abstract protected function getInlineContent();

    /**
     * @desc Returns the header object
     * @return \Zend_View_Helper_Placeholder_Container_Standalone An instance of
     * the standalone header
     */
    abstract public function getHeader();
}
