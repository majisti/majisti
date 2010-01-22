<?php

namespace Majisti\Model\Mail;

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

    public function setBodyObject(IBodyObject $object)
    {
        $this->_bodyObject = $object;
    }

    /**
     * @return bool
     */
    protected function isBodyObjectHtml()
    {
        $content = $this->getBodyObject()->getBody();

        return strlen($content) !== strlen(strip_tags($content));
    }

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
