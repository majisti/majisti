<?php
namespace Majisti\View\Helper\Head;
require_once 'TestHelper.php';

/**
 * @desc Tests that the stylesheet optimizer can bundle and minify
 * css stylesheets correctly.
 *
 * @author Majisti
 */
class AbstractHeadOptimizerTest extends \Majisti\Test\TestCase
{
static protected $_class = __CLASS__;

    /**
     * @var Array
     */
    public $files;

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
     * @var string
     */
    public $url = '/majisti/tests/library/Majisti/View/Helper/_files';

    /**
     * Setups the test case
     */
    public function setUp()
    {
        /* files path */
        $this->files = realpath(dirname(__FILE__) . '/../_files');

        $this->view = new \Zend_View();
        $this->view->addHelperPath(
            'Majisti/View/Helper',
            'Majisti_View_Helper'
        );

        /* optimizer options setting paths to styles and scripts */
        $options = array(
            'stylesPath'  => $this->files . '/styles',
            'scriptsPath' => $this->files . '/scripts',
        );

        $this->optimizer = new HeadLinkOptimizer($this->view->headLink(),
            $options);
        $this->optimizer->clearCache();

        /* clearing headlink data */
        $this->view->headLink()->exchangeArray(array());

        \Zend_Controller_Front::getInstance()->setRequest(
            new \Zend_Controller_Request_Http());

        /* setting optimizer jarfile and temporary directory */
        \Majisti\Util\Minifying\Yui::$jarFile = MAJISTI_ROOT .
             '/../externals/yuicompressor-2.4.2.jar';
        \Majisti\Util\Minifying\Yui::$tempDir = '/tmp';
    }
}