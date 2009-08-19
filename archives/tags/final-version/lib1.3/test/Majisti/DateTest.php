<?php

/*
 * Test helper
 */
require_once dirname(__FILE__) . '/../TestHelper.php';

/**
 * Test the Majisti_Date class
 *
 * @lastmodified 2009-02-26
 *
 * @author Yanick Rochon
 * @version 1
 */
class Majisti_DateTest extends Majisti_Test_PHPUnit_TestCase
{

	/**
	 * The Majisti_Date instance used throughout the tests
	 *
	 * @var Majisti_Date
	 */
	private $date;


	public function setUp() {
		$this->date = new Majisti_Date();
	}

	/**
	 * Test the weekday constants
	 */
	public function testDayOfWeekConstants()
	{
		$this->date->setWeekday(Majisti_Date::MONDAY);
		$this->assertEquals(1, Majisti_Date::MONDAY);
		$this->assertEquals(Majisti_Date::MONDAY, $this->date->get(Zend_Date::WEEKDAY_8601));

		$this->date->setWeekday(Majisti_Date::TUESDAY);
		$this->assertEquals(2, Majisti_Date::TUESDAY);
		$this->assertEquals(Majisti_Date::TUESDAY, $this->date->get(Zend_Date::WEEKDAY_8601));

		$this->date->setWeekday(Majisti_Date::WEDNESDAY);
		$this->assertEquals(3, Majisti_Date::WEDNESDAY);
		$this->assertEquals(Majisti_Date::WEDNESDAY, $this->date->get(Zend_Date::WEEKDAY_8601));

		$this->date->setWeekday(Majisti_Date::THURSDAY);
		$this->assertEquals(4, Majisti_Date::THURSDAY);
		$this->assertEquals(Majisti_Date::THURSDAY, $this->date->get(Zend_Date::WEEKDAY_8601));

		$this->date->setWeekday(Majisti_Date::FRIDAY);
		$this->assertEquals(5, Majisti_Date::FRIDAY);
		$this->assertEquals(Majisti_Date::FRIDAY, $this->date->get(Zend_Date::WEEKDAY_8601));

		$this->date->setWeekday(Majisti_Date::SATURDAY);
		$this->assertEquals(6, Majisti_Date::SATURDAY);
		$this->assertEquals(Majisti_Date::SATURDAY, $this->date->get(Zend_Date::WEEKDAY_8601));

		$this->date->setWeekday(Majisti_Date::SUNDAY);
		$this->assertEquals(7, Majisti_Date::SUNDAY);
		$this->assertEquals(Majisti_Date::SUNDAY, $this->date->get(Zend_Date::WEEKDAY_8601));

		// with Majisti_Date, Sunday may also be set using 0
		$this->date->setWeekday(0);
		$this->assertEquals(Majisti_Date::SUNDAY, $this->date->get(Zend_Date::WEEKDAY_8601));


	}

	/**
	 * Test getting the next weekday off the current date
	 */
	public function testGetNextWeekday()
	{
		$dateCopy = clone $this->date;

		// Set day of week (WEEKDAY_DIGIT where 0=Monday and 6=Saturday)
		for ($dayOfWeek = 0; $dayOfWeek <= 6; $dayOfWeek++) {
			$nextWeek = $this->date->getNextWeekday($dayOfWeek);
			// the date is actually a Friday
			$this->assertEquals($dayOfWeek, $nextWeek->get(Zend_Date::WEEKDAY_DIGIT));
			// we are in fact at the next friday!
			$this->assertTrue($nextWeek->isLater($this->date));
			// the original date hasn't changed!
			$this->assertTrue($this->date->equals($dateCopy));
		}

	}

	/**
	 * Test getting the previous weekday off the current date
	 *
	 */
	public function testGetPreviousWeekday()
	{
		$dateCopy = clone $this->date;

		// Set day of week (WEEKDAY_DIGIT where 0=Monday and 6=Saturday)
		for ($dayOfWeek = 0; $dayOfWeek <= 6; $dayOfWeek++) {
			$nextWeek = $this->date->getPreviousWeekday($dayOfWeek);
			// the date is actually a Friday
			$this->assertEquals($dayOfWeek, $nextWeek->get(Zend_Date::WEEKDAY_DIGIT));
			// we are in fact at the next friday!
			$this->assertTrue($nextWeek->isEarlier($this->date));
			// the original date hasn't changed!
			$this->assertTrue($this->date->equals($dateCopy));
		}
	}

	/**
	 * Test getting the next time value using different time format
	 */
	public function testGetNextTime()
	{
		$this->markTestIncomplete("TODO");
	}
	//TODO : public function testGetNext ...
	
}

Majisti_Test_Runner::run('Majisti_DateTest');
