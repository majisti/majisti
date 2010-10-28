<?php

namespace Symfony\Component\Validator\Mapping\Loader;

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Symfony\Component\Validator\Exception\MappingException;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Validator\Constraints\Validation;
use Symfony\Component\Validator\Constraint;

class AnnotationLoader implements LoaderInterface
{
    protected $reader;

    public function __construct()
    {
        $this->reader = new AnnotationReader();
        $this->reader->setAutoloadAnnotations(true);
        $this->reader->setAnnotationNamespaceAlias('Symfony\Component\Validator\Constraints\\', 'validation');
    }

    /**
     * {@inheritDoc}
     */
    public function loadClassMetadata(ClassMetadata $metadata)
    {
        $reflClass = $metadata->getReflectionClass();
        $loaded = false;

        foreach ($this->reader->getClassAnnotations($reflClass) as $constraint) {
            if ($constraint instanceof Validation) {
                foreach ($constraint->constraints as $constraint) {
                    $metadata->addConstraint($constraint);
                }
            } elseif ($constraint instanceof Constraint) {
                $metadata->addConstraint($constraint);
            }

            $loaded = true;
        }

        foreach ($reflClass->getProperties() as $property) {
            foreach ($this->reader->getPropertyAnnotations($property) as $constraint) {
                if ($constraint instanceof Validation) {
                    foreach ($constraint->constraints as $constraint) {
                        $metadata->addPropertyConstraint($property->getName(), $constraint);
                    }
                } elseif ($constraint instanceof Constraint) {
                    $metadata->addPropertyConstraint($property->getName(), $constraint);
                }

                $loaded = true;
            }
        }

        foreach ($reflClass->getMethods() as $method) {
            foreach ($this->reader->getMethodAnnotations($method) as $constraint) {
                // TODO: clean this up
                $name = lcfirst(substr($method->getName(), 0, 3)=='get' ? substr($method->getName(), 3) : substr($method->getName(), 2));

                if ($constraint instanceof Validation) {
                    foreach ($constraint->constraints as $constraint) {
                        $metadata->addGetterConstraint($name, $constraint);
                    }
                } elseif ($constraint instanceof Constraint) {
                    $metadata->addGetterConstraint($name, $constraint);
                }

                $loaded = true;
            }
        }

        return $loaded;
    }
}
