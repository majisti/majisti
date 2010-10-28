<?php

namespace Symfony\Component\Form;

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Symfony\Component\Form\FieldInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * @author     Bernhard Schussek <bernhard.schussek@symfony-project.com>
 */
class CollectionField extends FieldGroup
{
    /**
     * The prototype for the inner fields
     * @var FieldInterface
     */
    protected $prototype;

    /**
     * Repeats the given field twice to verify the user's input
     *
     * @param FieldInterface $innerField
     */
    public function __construct(FieldInterface $innerField, array $options = array())
    {
        $this->prototype = $innerField;

        parent::__construct($innerField->getKey(), $options);
    }

    protected function configure()
    {
        $this->addOption('modifiable', false);

        if ($this->getOption('modifiable')) {
            $field = $this->newField('$$key$$', null);
            // TESTME
            $field->setRequired(false);
            $this->add($field);
        }
    }

    public function setData($collection)
    {
        if (!is_array($collection) && !$collection instanceof \Traversable) {
            throw new UnexpectedTypeException('The data must be an array');
        }

        foreach ($this as $name => $field) {
            if (!$this->getOption('modifiable') || $name != '$$key$$') {
                $this->remove($name);
            }
        }

        foreach ($collection as $name => $value) {
            $this->add($this->newField($name, $name));
        }

        parent::setData($collection);
    }

    public function bind($taintedData)
    {
        if (is_null($taintedData)) {
            $taintedData = array();
        }

        foreach ($this as $name => $field) {
            if (!isset($taintedData[$name]) && $this->getOption('modifiable') && $name != '$$key$$') {
                $this->remove($name);
            }
        }

        foreach ($taintedData as $name => $value) {
            if (!isset($this[$name]) && $this->getOption('modifiable')) {
                $this->add($this->newField($name, $name));
            }
        }

        return parent::bind($taintedData);
    }

    protected function newField($key, $propertyPath)
    {
        $field = clone $this->prototype;
        $field->setKey($key);
        $field->setPropertyPath($propertyPath === null ? null : '['.$propertyPath.']');
        return $field;
    }
}