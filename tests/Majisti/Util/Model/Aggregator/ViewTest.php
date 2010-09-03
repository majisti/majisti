<?php

namespace Majisti\Util\Model\Aggregator;

require_once 'TestHelper.php';

/**
 * @desc Test the view class.
 * @author Majisti
 */
class ViewTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var \Config
     */
    public $aggregator;

    /**
     * @var \Zend_Config
     */
    public $view;

    /**
     * @desc Setups the test case
     */
    public function setUp()
    {
        $this->view         = new \Zend_Config(array('foo' => 'bar'));
        $this->aggregator   = new View();
    }

    /**
     * Asserts accessors and mutators
     * @expectedException Exception
     */
    public function testGetSetView()
    {
        $aggregator = $this->aggregator;
        $view       = $this->view;

        $aggregator->setView($view);
        $this->assertEquals($view, $aggregator->getView());

        /* throws exception */
        $aggregator->setView(array());
    }
}

ViewTest::runAlone();
