<?php

namespace Majisti\View\Helper\Head;

require_once 'TestHelper.php';

/**
 * @desc Tests that the stylesheet optimizer can bundle and minify
 * css stylesheets correctly.
 *
 * @author Majisti
 */
abstract class AbstractHeadOptimizerTest extends \Majisti\Test\TestCase
{

    static protected $_class = __CLASS__;

    /**
     * @var string
     */
    public $filesPath;

    /**
     * @var string
     */
    public $filesUrl;

    /**
     * @var \Zend_View
     */
    public $view;

    /**
     * @var HeadLinkOptimizer
     */
    public $linksOptimizer;

    /**
     * @var HeadScriptsOptimizer
     */
    public $scriptsOptimizer;

    /**
     * @var \StdClass
     */
    protected $options;

    public function __construct()
    {
        /* optimizer options setting paths to styles and scripts */
        $this->options          = new \StdClass();
        $this->options->styles  = array('path' => $this->filesPath . '/styles');
        $this->options->scripts = array('path' => $this->filesPath . '/scripts');

        $this->filesPath = realpath(dirname(__FILE__) . '/../_files');
        $this->filesUrl  = '/majisti/tests/library/Majisti/View/Helper/_files';
    }

    /**
     * Setups the test case
     */
    public function setUp()
    {
        /* view instanciation */
        $this->view = new \Zend_View();
        $this->view->addHelperPath(
            'Majisti/View/Helper',
            'Majisti_View_Helper'
        );

        /* optimizers instanciation */
        $this->linksOptimizer = new HeadLinkOptimizer($this->view,
                $options->styles);
        $this->linksOptimizer->clearCache();

        $this->scriptsOptimizer = new HeadScriptOptimizer($this->view,
                $options->scripts);
        $this->scriptsOptimizer->clearCache();

        /* clearing head data */
        $this->clearHead();

        \Zend_Controller_Front::getInstance()->setRequest(
            new \Zend_Controller_Request_Http());

        /* setting optimizer jarfile and temporary directory */
        \Majisti\Util\Minifying\Yui::$jarFile = MAJISTI_ROOT .
             '/../externals/yuicompressor-2.4.2.jar';
        \Majisti\Util\Minifying\Yui::$tempDir = '/tmp';
    }

    /**
     * @desc Unit test tear down: removing the bundle() and minify() output
     * files and clearing the headlink object.
     */
    public function tearDown()
    {
        $files   = array('themes.css', 'all.css', 'all.min.css', 'all.min.js');
        $styles  = array('theme1.min.css', 'theme2.min.css', 'core.min.css');
        $scripts = array('script1.min.css', 'script2.min.css', 'script2.min.css');

        foreach($files as $file) {
            @unlink($this->files . "/{$file}");
        }

        foreach($styles as $style) {
            @unlink($this->files . "/styles/{$style}");
        }

        foreach($scripts as $script) {
            @unlink($this->files . "/scripts/{$script}");
        }

        $this->clearHead();
    }


    /**
     * @desc TODO
     */
    protected function getOptions($optimizer)
    {
        return $this->options->$optimizer;
    }

    /**
     * @desc TODO
     */
    protected function clearHead()
    {
        $this->view->headLink()->exchangeArray(array());
        $this->view->headScripts()->exchangeArray(array());
    }
}