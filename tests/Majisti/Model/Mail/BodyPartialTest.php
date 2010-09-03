<?php

namespace Majisti\Model\Mail;

require_once 'TestHelper.php';

/**
 * @desc Tests the body partial class
 *
 * @author Steven Rosato
 */
class BodyPartialTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var \Majisti\View
     */
    public $view;

    /**
     * @var \StdClass
     */
    public $model;

    /**
     * @var BodyPartial
     */
    public $bodyPartial;

    /**
     * @var BodyPartial
     */
    public $bodyPartialWithModel;

    /**
     * @var BodyPartial
     */
    public $bodyPartialWithNoView;

    /**
     * @desc Setups the test case
     */
    public function setUp()
    {
        $this->view = new \Majisti\View();
        $this->view->addScriptPath(__DIR__ . '/_partials');

        $this->bodyPartial = new BodyPartial('simple.phtml', $this->view);
        $this->bodyPartialWithNoView = new BodyPartial('simple.phtml');

        $this->model = new \StdClass();
        $this->model->name = 'Majisti';
        $this->bodyPartialWithModel = new BodyPartial(
            'model.phtml', $this->view, $this->model);

        \Zend_Registry::set('Zend_View', $this->view);
    }

    public function testGetters()
    {
        /* assert bodypartials getters with or without a view */
        foreach (array('bodyPartial', 'bodyPartialWithNoView') as $partial) {
        	$this->assertEquals('simple.phtml', $this->$partial->getPartialName());
            $this->assertEquals($this->view, $this->$partial->getView());
            $this->assertNull($this->$partial->getModel());
        }

        /* assert bodypartial getters with a model */
        $bodyPartialModel = $this->bodyPartialWithModel;
        $this->assertEquals('model.phtml', $bodyPartialModel->getPartialName());
        $this->assertEquals($this->view,   $bodyPartialModel->getView());
        $this->assertEquals($this->model,  $bodyPartialModel->getModel());
    }

    public function testGetBodyRendersAndReturnsPartialContent()
    {
        /* all partials should return the expected content */
        $expectedContent = '<div><p>Hello, Majisti.</p></div>';
        $this->assertEquals($expectedContent, $this->bodyPartial->getBody());
        $this->assertEquals($expectedContent, $this->bodyPartialWithModel->getBody());
    }
}

BodyPartialTest::runAlone();
