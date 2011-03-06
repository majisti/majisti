<?php

namespace Majisti\Application\Resource;

require_once __DIR__ . '/TestHelper.php';

/**
 * @desc Asserts that the view resource setups a default view for Majisti
 * applications.
 *
 * @author Majisti
 */
class ViewTest extends \Majisti\Test\TestCase
{
    /**
     * @var \Majisti\Application\Resource\View
     */
    public $resource;

    /**
     * @var Array
     */
    public $expectedPaths = array(
        'Zend_View_Helper_'             => array('Zend/View/Helper/'),
        'Majisti\View\Helper\\'         => array('Majisti/View/Helper/'),
        'MajistiT\View\Helper\\'        => array('/views/helpers/'),
    );

    /**
     * @desc Setups the test case
     */
    public function setUp()
    {
        $helper  = $this->getHelper();
        $options = $helper->getOptions();

        /* prepend library paths to those keys */
        $keys = array('MajistiT\View\Helper\\');
        foreach( $keys as $key ) {
            $this->expectedPaths[$key][0] =
                $options['majisti']['app']['path'] .
                    '/lib' . $this->expectedPaths[$key][0];
        }
    }

    /**
     * @desc Creates the resource.
     * @param array $options The options
     *
     * @return View The view
     */
    protected function createResource($options = null)
    {
        $view = new View($options);
        $view->setBootstrap($this->getHelper()->createBootstrapInstance());

        return $view;
    }

    /**
     * @desc Asserts that the view gets initialized correctly
     */
    public function testInit()
    {
        $resource = $this->createResource();
        $view = $resource->init();

        $this->assertEquals(\Zend_View_Helper_Doctype::XHTML1_STRICT,
                $view->doctype()->getDoctype());

        $this->assertTrue(\Zend_Registry::isRegistered('Zend_View'));
    }
}

ViewTest::runAlone();
