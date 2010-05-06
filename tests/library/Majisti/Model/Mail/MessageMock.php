<?php

namespace Majisti\Model\Mail;

/**
 * @desc Mock class for Message class.
 *
 * @author Majisti
 */
class MessageMock extends Message
{
    /**
     * @desc Mocks the send call. Assert only what has been done before.
     */
    public function send($transport = null)
    {
        try {
            parent::send($transport);
        } catch( \Zend_Mail_Transport_Exception $e ) { }
    }
}
