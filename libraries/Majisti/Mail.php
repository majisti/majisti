<?php

namespace Majisti;

class Mail extends \Zend_Mail
{
    static protected $_defaultSubjectPrefix = '';

    protected $_subjectPrefix = '';

    static public function clearDefaultSubjectPrefix()
    {
        static::$_defaultSubjectPrefix = '';
    }

    static public function setDefaultSubjectPrefix($prefix)
    {
        static::$_defaultSubjectPrefix = $prefix;
    }

    static public function getDefaultSubjectPrefix()
    {
        return static::$_defaultSubjectPrefix;
    }

    public function clearSubjectPrefix()
    {
        $this->_subjectPrefix = '';
    }

    public function setSubjectPrefix($prefix)
    {
        $this->_subjectPrefix = $prefix;
    }

    public function getSubjectPrefix()
    {
        return $this->_subjectPrefix;
    }

    public function setSubject($subject, $withoutPrefix = false)
    {
        if( $withoutPrefix ) {
            return parent::setSubject($subject);
        }

        $prefix = $this->getSubjectPrefix();

        if( empty($prefix) ) {
            $prefix = static::getDefaultSubjectPrefix();
        }

        return parent::setSubject($prefix . $subject);
    }
}
