<?php

namespace Majisti\Model\Mail;

/**
 * @desc Message model extending \Zend_Mail to offer the developper a more
 * object-oriented way of populating a message's body. The body is now built
 * with a body object of type IBodyObject. This way, the message body is also
 * more flexible, only relying on the getBody() function, wich can be customized
 * to return multiple message templates.
 *
 * @author Majisti
 */
class Message extends \Zend_Mail
{
    protected $_bodyObject;

    /**
     * @return IBodyObject
     */
    public function getBodyObject()
    {
        return $this->_bodyObject;
    }

    /**
     * @desc Body object setter
     * @param IBodyObject $object
     */
    public function setBodyObject(IBodyObject $object)
    {
        $this->_bodyObject = $object;
    }

    /**
     * @desc Checks whether a body object is made of HTML content or flat-
     * text by comparing it's original length with the length it has after
     * having it's tags stripped.
     * @return true if the body object is HTML, false otherwise
     */
    protected function isBodyObjectHtml()
    {
        $content = $this->getBodyObject()->getBody();

        return strlen($content) !== strlen(strip_tags($content));
    }

    /**
     * @desc Overriding \Zend_Mail send function to set the body type
     * accordingly to the local body object.
     * @param \Zend_Mail_Transport_Abstract $transport
     */
    public function send($transport = null)
    {
        $body = $this->getBodyObject();

        if( null !== $body ) {
            if( $this->isBodyObjectHtml() ) {
                $this->setBodyHtml($body->getBody());
            } else {
                $this->setBodyText($body->getBody());
            }
        }

        parent::send($transport);
    }
}
