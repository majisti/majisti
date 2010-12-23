<?php

namespace Majisti\Model\Mail;

require_once __DIR__ . '/TestHelper.php';

/**
 * @desc Tests the Message class.
 * @author Majisti
 */
class MessageTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;

    /** @var \Majisti\View */
    public $view;

    /** @var BodyPartial */
    public $bodyPartial;

    /** @var BodyPartial */
    public $bodyPartialWithoutView;

    /** @var \StdClass */
    public $model;

    /** @var BodyPartial */
    public $bodyPartialWithModel;

    /** @var BodyPartial */
    public $bodyPartialFlatText;

    /** @var MessageMock */
    public $message;

    /**
     * Setups the test case
     */
    public function setUp()
    {
        $this->message = new MessageMock();

        $this->view = new \Majisti\View\View();
        $this->view->addScriptPath(__DIR__ . '/_partials');

        $this->model = new \StdClass();
        $this->model->name = 'Majisti';

        $this->bodyPartial = new BodyPartial('simple.phtml', $this->view);
        $this->bodyPartialWithoutView = new BodyPartial('simple.phtml');
        $this->bodyPartialWithModel = new BodyPartial(
            'model.phtml', $this->view, $this->model);
        $this->bodyPartialFlatText = new BodyPartial('flat.phtml');

        \Zend_Registry::set('Zend_View', $this->view);
    }

    /**
     * Testing that getBodyObject() returns the right body object with the
     * right content.
     */
    public function testGetBodyObjects()
    {
        $partials = array($this->bodyPartial, $this->bodyPartialWithoutView,
            $this->bodyPartialWithModel, $this->bodyPartialFlatText);

        $partialNames = new \Majisti\Util\Model\Collection\Stack();
        $partialNames->push(array_reverse(array('simple.phtml', 'simple.phtml', 
            'model.phtml', 'flat.phtml')));

        $bodyObject = $this->message->getBodyObject();
        $this->assertNull($bodyObject);

        foreach ($partials as $partial) {
            $this->message->setBodyObject($partial);
            $bodyObject = $this->message->getBodyObject();
            $this->assertEquals($partial->getBody(), $bodyObject->getBody());
            $this->assertEquals($partialNames->pop(),
                    $partial->getPartialName());
        }
    }

    /**
     * Testing that send() function sets the right type of body before sending
     * with the use of a mock object.
     */
    public function testSendingFunction()
    {
        $array = array($this->bodyPartial, $this->bodyPartialWithoutView,
             $this->bodyPartialWithModel, $this->bodyPartialFlatText);

        foreach($array as $partial) {
            $this->message->setBodyObject($partial);
            $this->message->send();

            if( 'flat.phtml' !== $partial->getPartialName() ) {
                $this->assertEquals($partial->getBody(),
                        $this->message->getBodyHtml()->getContent());
                $this->assertFalse($this->message->getBodyText());
            } else {
                $this->assertEquals($partial->getBody(),
                        $this->message->getBodyText()->getContent());
            }
        }
    }
}

MessageTest::runAlone();
