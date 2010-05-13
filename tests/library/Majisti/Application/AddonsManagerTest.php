<?php

namespace Majisti\Application;

require_once 'TestHelper.php';

/**
 * @desc
 * @author Majisti
 */
class AddonsManagerTest extends \Majisti\Test\TestCase
{
    static protected $_class = __CLASS__;

    /**
     * @var AddonsManager
     */
    public $manager;

    /**
     * Setups the test case
     */
    public function setUp()
    {

    }
}

AddonsManagerTest::runAlone();
