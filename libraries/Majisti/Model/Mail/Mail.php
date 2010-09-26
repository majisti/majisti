<?php

namespace Majisti\Model\Mail;

/**
 * @desc Majisti Mail that supports prefixed mail subjects
 * such as [Majisti].
 *
 * @author Majisti
 */
class Mail extends \Zend_Mail
{
    static protected $_defaultSubjectPrefix = '';

    protected $_subjectPrefix = '';

    /**
     * Public constructor
     *
     * @param string $charset
     */
    public function __construct($charset = 'UTF-8')
    {
        parent::__construct($charset);
    }

    /**
     * @desc Clears the default subject prefix
     */
    static public function clearDefaultSubjectPrefix()
    {
        static::$_defaultSubjectPrefix = '';
    }

    /**
     * @desc Sets the default subject prefix for all instances.
     *
     * @param string $prefix The prefix
     */
    static public function setDefaultSubjectPrefix($prefix)
    {
        static::$_defaultSubjectPrefix = $prefix;
    }

    /**
     * @desc Returns the default subject prefix for all instances.
     *
     * @return string The default subject prefix
     */
    static public function getDefaultSubjectPrefix()
    {
        return static::$_defaultSubjectPrefix;
    }

    /**
     * @desc Clears the subject prefix
     *
     * @return Mail This
     */
    public function clearSubjectPrefix()
    {
        $this->_subjectPrefix = '';

        return $this;
    }

    /**
     * @desc Sets the subject prefix.
     *
     * @param string $prefix The subject prefix
     *
     * @return Mail This
     */
    public function setSubjectPrefix($prefix)
    {
        $this->_subjectPrefix = $prefix;

        return $this;
    }

    /**
     * @desc Returns the subject prefix.
     *
     * @return string The subject prefix
     */
    public function getSubjectPrefix()
    {
        return $this->_subjectPrefix;
    }

    /**
     * @desc Sets the subject of the message.
     *
     * @param string $subject The subject
     * @param bool $withoutPrefix Whether to omit the subject prefix
     *
     * @return string The subject
     */
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
