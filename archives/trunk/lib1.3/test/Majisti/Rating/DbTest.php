<?php

require_once dirname(__FILE__) . '/../TestHelper.php';

/**
 * Majisti_Rating_Db test case.
 */
class Majisti_Rating_DbTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Majisti_Rating_Db
	 */
	private $Majisti_Rating_Db;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated Majisti_Rating_DbTest::setUp()
		

		$this->Majisti_Rating_Db = new Majisti_Rating_Db(/* parameters */);
	
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated Majisti_Rating_DbTest::tearDown()
		

		$this->Majisti_Rating_Db = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests Majisti_Rating_Db->__construct()
	 */
	public function test__construct() {
		// TODO Auto-generated Majisti_Rating_DbTest->test__construct()
		$this->markTestIncomplete ( "__construct test not implemented" );
		
		$this->Majisti_Rating_Db->__construct(/* parameters */);
	
	}
	
	/**
	 * Tests Majisti_Rating_Db->getAverageRating()
	 */
	public function testGetAverageRating() {
		// TODO Auto-generated Majisti_Rating_DbTest->testGetAverageRating()
		$this->markTestIncomplete ( "getAverageRating test not implemented" );
		
		$this->Majisti_Rating_Db->getAverageRating(/* parameters */);
	
	}
	
	/**
	 * Tests Majisti_Rating_Db->getScale()
	 */
	public function testGetScale() {
		// TODO Auto-generated Majisti_Rating_DbTest->testGetScale()
		$this->markTestIncomplete ( "getScale test not implemented" );
		
		$this->Majisti_Rating_Db->getScale(/* parameters */);
	
	}
	
	/**
	 * Tests Majisti_Rating_Db->getTotalRating()
	 */
	public function testGetTotalRating() {
		// TODO Auto-generated Majisti_Rating_DbTest->testGetTotalRating()
		$this->markTestIncomplete ( "getTotalRating test not implemented" );
		
		$this->Majisti_Rating_Db->getTotalRating(/* parameters */);
	
	}
	
	/**
	 * Tests Majisti_Rating_Db->getTotalVotes()
	 */
	public function testGetTotalVotes() {
		// TODO Auto-generated Majisti_Rating_DbTest->testGetTotalVotes()
		$this->markTestIncomplete ( "getTotalVotes test not implemented" );
		
		$this->Majisti_Rating_Db->getTotalVotes(/* parameters */);
	
	}
	
	/**
	 * Tests Majisti_Rating_Db->hasRated()
	 */
	public function testHasRated() {
		// TODO Auto-generated Majisti_Rating_DbTest->testHasRated()
		$this->markTestIncomplete ( "hasRated test not implemented" );
		
		$this->Majisti_Rating_Db->hasRated(/* parameters */);
	
	}
	
	/**
	 * Tests Majisti_Rating_Db->rate()
	 */
	public function testRate() {
		// TODO Auto-generated Majisti_Rating_DbTest->testRate()
		$this->markTestIncomplete ( "rate test not implemented" );
		
		$this->Majisti_Rating_Db->rate(/* parameters */);
	
	}

}

Majisti_Test_Runner::run('Majisti_Rating_DbTest');
