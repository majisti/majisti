<?php

namespace Majisti\Controller\Plugin;

require_once 'TestHelper.php';

/**
 * @desc
 * @author
 */
class EnvironmentDeployerTest extends \Majisti\Test\PHPUnit\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var EnvironmentSwitcher
     */
    public $envDeployer;

    /**
     * @var \Zend\View\Helper\HeadLink
     */
    public $headLink;

    /**
     * @var String
     */
    public $filesPath;

    /**
     * @desc Setups the test case
     */
    public function setUp()
    {
        $this->envDeployer  = new EnvironmentDeployer();
        $this->headLink     = new \Zend_View_Helper_HeadLink();
        $this->filesPath    = dirname(__FILE__) . '/_files';

        $this->prepareCss();
    }

    protected function prepareCss()
    {
        $this->headLink->appendStylesheet($this->filesPath . '/core.css');
        $this->headLink->appendStylesheet($this->filesPath . '/themeA.css');
        $this->headLink->appendStylesheet($this->filesPath . '/themeB.css');
    }

    public function tearDown()
    {
//        @unlink($this->filesPath . '/generated/master.css');
    }

    public function testBundleCssGeneratesFile()
    {
        $masterCss = $this->filesPath . '/generated/master.css';
        $this->envDeployer->bundleCss($masterCss, '/master.css');

        $this->assertTrue(file_exists($masterCss));
        $this->assertEquals(
            '.classCore{color:#000;}.classA{color:#AAA;}.classB{color:#BBB;}',
            file_get_contents($masterCss)
        );
    }
}

EnvironmentDeployerTest::runAlone();
