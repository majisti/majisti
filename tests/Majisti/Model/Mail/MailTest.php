<?php

namespace Majisti\Model\Mail;

require_once __DIR__ . '/TestHelper.php';

if( !defined('PHPUnit_MAIN_METHOD') ) {
    define("PHPUnit_MAIN_METHOD", false);
}

/**
 * @desc Test Majisti's Mail class.
 *
 * @author Majisti
 */
class MailTest extends \Zend_Mail_MailTest
{
    static public function runAlone()
    {
        \Majisti\Test\TestCase::setClass(__CLASS__);
        \Majisti\Test\TestCase::runAlone();
    }

    public function setUp()
    {
        parent::setUp();
        Mail::clearDefaultSubjectPrefix();
    }

    /**
     * @desc Tests a given instance for subject prefixing.
     *
     * @param Mail $mail The mail instance
     */
    protected function instanceSubjectPrefix(Mail $mail)
    {
        $prefix  = "[Majisti] ";
        $subject = "A subject";

        $mail->setSubjectPrefix($prefix);
        $mail->setSubject($subject);
        $this->assertEquals($prefix . $subject, $mail->getSubject());

        $mail->clearSubject();

        $mail->setSubject($subject, true);
        $this->assertEquals($subject, $mail->getSubject());

        $mail->clearSubjectPrefix();
        $this->assertEquals($subject, $mail->getSubject());
    }

    public function testCharsetIsUtf8ByDefault()
    {
        $mail = new Mail();
        $this->assertEquals('UTF-8', $mail->getCharset());
    }

    public function testSubjectPrefix()
    {
        $this->instanceSubjectPrefix(new Mail());
    }

    public function testDefaultSubjectPrefix()
    {
        $prefix  = "[Default Majisti] ";
        $subject = "Default subject";

        Mail::setDefaultSubjectPrefix($prefix);
        $mail = new Mail();

        /* setting no subject won't return anything, not even the prefix */
        $this->assertEquals('', $mail->getSubject());

        $mail->setSubject($subject);
        $this->assertEquals($prefix . $subject, $mail->getSubject());

        $mail->clearSubject();

        /* instance subject setters have predominance over default prefix */
        $this->instanceSubjectPrefix($mail);

        $this->assertEquals($prefix, Mail::getDefaultSubjectPrefix());

        Mail::clearDefaultSubjectPrefix();
        $this->assertEquals('', Mail::getDefaultSubjectPrefix());

        $mail = new Mail();
        $this->assertEquals('', $mail->getSubject());
    }
}

MailTest::runAlone();
