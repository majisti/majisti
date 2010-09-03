<?php

namespace Majisti\Model\Mail;

/**
 * @desc A BodyPartial is simply a class that will return the content of
 * a partial script called from a view. A model can optionnaly be provided
 * to the partial.
 *
 * @author Steven Rosato
 */
class BodyPartial extends \Majisti\Util\Model\Aggregator\View
    implements IBodyObject
{
    /**
     * @var string
     */
    protected $_partialName;

    /**
     * @desc Model that will be passed to the partial view
     * @var mixed
     */
    protected $_model;

    /**
     * @desc Constructs the BodyPartial with a view and a model. If no view
     * is provided, the registered Zend_View will be taken from
     * the Zend_Registry, if any.
     *
     * @param $partialName The partial name, the view should be able to call
     *  the script.
     * @param $view The view
     * @param $model The model that will be given to the partial view
     * when {@link BodyPartial::getBody()} is called
     */
    public function __construct($partialName, \Zend_View_Interface $view = null,
            $model = null)
    {
        $this->setPartialName($partialName);

        if( null !== $view ) {
            $this->setView($view);
        }

        $this->setModel($model);
    }

    /**
     * @desc Returns the partial name
     *
     * @return string the partial name
     */
    public function getPartialName()
    {
        return $this->_partialName;
    }

    /**
     * @desc Sets the partial name
     * @param $partialName The partial name
     */
    public function setPartialName($partialName)
    {
        $this->_partialName = $partialName;
    }

    /**
     * @desc Returns the model that will be given to the partial script
     * when calling {@link BodyPartial::getBody()}
     *
     * @return The model
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @desc Sets a model that will be given to the partial scripts
     * when calling {@link BodyPartial::getBody()}
     *
     * @param $model The model
     */
    public function setModel($model)
    {
        $this->_model = $model;
    }

    /**
     * @desc Calls the partial from the view provided and returns
     * the rendered content.
     *
     * @return string The rendered content
     */
    public function getBody()
    {
        return $this->getView()->partial(
            $this->getPartialName(), $this->getModel());
    }
}