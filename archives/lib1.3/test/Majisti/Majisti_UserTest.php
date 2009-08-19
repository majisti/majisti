<?php

/* Test helper */
require_once dirname(__FILE__) . '/../TestHelper.php';

/**
 * Majisti_User test case.
 */
class Majisti_UserTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Majisti_User
	 */
	private $Majisti_User;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		// TODO Auto-generated Majisti_UserTest::setUp()
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated Majisti_UserTest::tearDown()
		

		$this->Majisti_User = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Tests Majisti_User->__get()
	 */
	public function test__get() {
		// TODO Auto-generated Majisti_UserTest->test__get()
		$this->markTestIncomplete ( "__get test not implemented" );
		
		$this->Majisti_User->__get(/* parameters */);
	
	}
	
	/**
     * Ensures that the Singleton pattern is implemented properly
     *
     * @return void
     */
    public function testSingleton()
    {
        $this->assertTrue(Majisti_User::getInstance() instanceof Majisti_User);
        $this->assertEquals(Majisti_User::getInstance(), Majisti_User::getInstance());
    }
	
	/**
	 * Tests Majisti_User->checkForAdminInRoles()
	 */
	public function testCheckForAdminInRoles() {
		// TODO Auto-generated Majisti_UserTest->testCheckForAdminInRoles()
		$this->markTestIncomplete ( "checkForAdminInRoles test not implemented" );
		
		$this->Majisti_User->checkForAdminInRoles(/* parameters */);
	
	}
	
	/**
	 * Tests Majisti_User::getInstance()
	 */
	public function testGetInstance() {
		// TODO Auto-generated Majisti_UserTest::testGetInstance()
		$this->markTestIncomplete ( "getInstance test not implemented" );
		
		Majisti_User::getInstance(/* parameters */);
	
	}
	
	/**
	 * Tests Majisti_User->isAdmin()
	 */
	public function testIsAdmin() {
		// TODO Auto-generated Majisti_UserTest->testIsAdmin()
		$this->markTestIncomplete ( "isAdmin test not implemented" );
		
		$this->Majisti_User->isAdmin(/* parameters */);
	
	}
	
	/**
	 * Tests Majisti_User->profile()
	 */
	public function testProfile() {
		// TODO Auto-generated Majisti_UserTest->testProfile()
		$this->markTestIncomplete ( "profile test not implemented" );
		
		$this->Majisti_User->profile(/* parameters */);
	
	}
	
	/**
	 * Tests Majisti_User->role()
	 */
	public function testRole() {
		// TODO Auto-generated Majisti_UserTest->testRole()
		$this->markTestIncomplete ( "role test not implemented" );
		
		$this->Majisti_User->role(/* parameters */);
	
	}
	
	/**
	 * Tests Majisti_User->roles()
	 */
	public function testRoles() {
		// TODO Auto-generated Majisti_UserTest->testRoles()
		$this->markTestIncomplete ( "roles test not implemented" );
		
		$this->Majisti_User->roles(/* parameters */);
	
	}
	
	/**
	 * Tests Majisti_User->setDefaultData()
	 */
	public function testSetDefaultData() {
		// TODO Auto-generated Majisti_UserTest->testSetDefaultData()
		$this->markTestIncomplete ( "setDefaultData test not implemented" );
		
		$this->Majisti_User->setDefaultData(/* parameters */);
	
	}
	
	/**
	 * Tests Majisti_User->setIsAdmin()
	 */
	public function testSetIsAdmin() {
		// TODO Auto-generated Majisti_UserTest->testSetIsAdmin()
		$this->markTestIncomplete ( "setIsAdmin test not implemented" );
		
		$this->Majisti_User->setIsAdmin(/* parameters */);
	
	}
	
	/**
	 * Tests Majisti_User->setProfileData()
	 */
	public function testSetProfileData() {
		// TODO Auto-generated Majisti_UserTest->testSetProfileData()
		$this->markTestIncomplete ( "setProfileData test not implemented" );
		
		$this->Majisti_User->setProfileData(/* parameters */);
	
	}
	
	/**
	 * Tests Majisti_User->setRoles()
	 */
	public function testSetRoles() {
		// TODO Auto-generated Majisti_UserTest->testSetRoles()
		$this->markTestIncomplete ( "setRoles test not implemented" );
		
		$this->Majisti_User->setRoles(/* parameters */);
	
	}

}

Majisti_Test_Runner::run('Majisti_UserTest');
