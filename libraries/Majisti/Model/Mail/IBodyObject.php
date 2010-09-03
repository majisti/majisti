<?php

namespace Majisti\Model\Mail;

/**
 * Message body object interface supplying getBody() functio signature.
 */
interface IBodyObject
{
    /**
     * @desc Determining what body is returned to the Message class before the
     * message gets sent, depending on concrete implementations.
     * @return string The body
     */
    public function getBody();
}
