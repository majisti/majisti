<?php

namespace Symfony\Component\Security\Authorization;

use Symfony\Component\Security\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Exception\AccessDeniedException;
use Symfony\Component\Security\Exception\InsufficientAuthenticationException;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * AccessDecisionManagerInterface makes authorization decisions.
 *
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 */
interface AccessDecisionManagerInterface
{
    /**
     * Decides whether the access is possible or not.
     *
     * @param TokenInterface $token      A TokenInterface instance
     * @param array          $attributes An array of attributes associated with the method being invoked
     * @param object         $object     The object to secure
     *
     * @return Boolean true if the access is granted, false otherwise
     */
    function decide(TokenInterface $token, array $attributes, $object = null);

    /**
     * Checks if the access decision manager supports the given attribute.
     *
     * @param string $attribute An attribute
     *
     * @return Boolean true if this decision manager supports the attribute, false otherwise
     */
    function supportsAttribute($attribute);

    /**
     * Checks if the access decision manager supports the given class.
     *
     * @param string $class A class name
     *
     * @return true if this decision manager can process the class
     */
    function supportsClass($class);
}
