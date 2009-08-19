<?php

/* Test helper */
require_once dirname(__FILE__) . '/../TestHelper.php';

/**
 * Majisti_Config_Xml test case.
 */
class Majisti_Config_XmlTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Majisti_Config_Xml
	 */
	private $Majisti_Config_Xml;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated Majisti_Config_XmlTest::setUp()
		

		$this->Majisti_Config_Xml = new Majisti_Config_Xml(/* parameters */);
	
	}
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated Majisti_Config_XmlTest::tearDown()
		

		$this->Majisti_Config_Xml = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests Majisti_Config_Xml->__construct()
	 */
	public function test__construct() {
		// TODO Auto-generated Majisti_Config_XmlTest->test__construct()
		$this->markTestIncomplete ( "__construct test not implemented" );
		
		$this->Majisti_Config_Xml->__construct(/* parameters */);
	
	}
	
	/**
	 * Tests Majisti_Config_Xml->getProperties()
	 */
	public function testGetProperties() {
		// TODO Auto-generated Majisti_Config_XmlTest->testGetProperties()
		$this->markTestIncomplete ( "getProperties test not implemented" );
		
		$this->Majisti_Config_Xml->getProperties(/* parameters */);
	
	}
	
	/**
	 * Tests Majisti_Config_Xml->getProperty()
	 */
	public function testGetProperty() {
		// TODO Auto-generated Majisti_Config_XmlTest->testGetProperty()
		$this->markTestIncomplete ( "getProperty test not implemented" );
		
		$this->Majisti_Config_Xml->getProperty(/* parameters */);
	
	}
	
	/**
	 * Tests Majisti_Config_Xml->mergeProperties()
	 */
	public function testMergeProperties() {
		// TODO Auto-generated Majisti_Config_XmlTest->testMergeProperties()
		$this->markTestIncomplete ( "mergeProperties test not implemented" );
		
		$this->Majisti_Config_Xml->mergeProperties(/* parameters */);
	
	}
	
	/**
	 * Tests Majisti_Config_Xml->reparse()
	 */
	public function testReparse() {
		// TODO Auto-generated Majisti_Config_XmlTest->testReparse()
		$this->markTestIncomplete ( "reparse test not implemented" );
		
		$this->Majisti_Config_Xml->reparse(/* parameters */);
	
	}
}

Majisti_Test_Runner::run('Majisti_Config_XmlTest');
