<?php

/*
 * Test helper
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . str_repeat('..'.DIRECTORY_SEPARATOR, 3) . 'TestHelper.php';

/**
 * Test the Majisti_Scheduler
 *
 * @lastmodified 2009-03-27
 *
 * @author Yanick Rochon
 * @version 1
 */
class Majisti_Scheduler_Task_RuleTest extends Majisti_Test_PHPUnit_TestCase
{

	const INTERVAL_DAILY = 5;
	const INTERVAL_WEEKLY = 3;
	const INTERVAL_MONTHLY = 6;
	const INTERVAL_YEARLY = 4;
	
	const CONSTRAINT_COUNT = 10;
	
	/**
	 * @var Majisti_Scheduler_Task_Rule 
	 */
	private $ruleDailySimple;
	/**
	 * @var Majisti_Scheduler_Task_Rule 
	 */
	private $ruleWeeklySimple;
	/**
	 * @var Majisti_Scheduler_Task_Rule 
	 */
	private $ruleMonthlySimple;
	/**
	 * @var Majisti_Scheduler_Task_Rule 
	 */
	private $ruleYearlySimple;
	
	
	public function setUp() {
		set_time_limit( 10 ); // give 10 seconds per test
		
		// all tests use YYYY-MM-DD
		$locale = new Zend_Locale('fr');
		Zend_Registry::set('Zend_Locale', $locale);
		
		// no start / expiration date or intervals
		$this->ruleDailySimple = new Majisti_Scheduler_Task_Rule(array(
			'type' => Majisti_Scheduler_Task_Rule::RULE_DAILY
		));
		$this->ruleWeeklySimple = new Majisti_Scheduler_Task_Rule(array(
			'type' => Majisti_Scheduler_Task_Rule::RULE_WEEKLY
		));
		$this->ruleMonthlySimple = new Majisti_Scheduler_Task_Rule(array(
			'type' => Majisti_Scheduler_Task_Rule::RULE_MONTHLY
		));
		$this->ruleYearlySimple = new Majisti_Scheduler_Task_Rule(array(
			'type' => Majisti_Scheduler_Task_Rule::RULE_YEARLY
		));
	}

	/**
	 * Validate the each type is at it's proper setting
	 */
	public function testType() {
		$this->assertEquals(Majisti_Scheduler_Task_Rule::RULE_DAILY, $this->ruleDailySimple->getType());		
		$this->assertEquals(Majisti_Scheduler_Task_Rule::RULE_WEEKLY, $this->ruleWeeklySimple->getType());
		$this->assertEquals(Majisti_Scheduler_Task_Rule::RULE_MONTHLY, $this->ruleMonthlySimple->getType());
		$this->assertEquals(Majisti_Scheduler_Task_Rule::RULE_YEARLY, $this->ruleYearlySimple->getType());
	}
	
	
	/**
	 * Test whether the task can be run today with no restriction
	 * or interval.
	 */
	public function testTodaySimple() {
		
		// the rule should satisfy with no last runs
		$this->assertTrue( $this->ruleDailySimple->isSatisfied(null) );
		$this->assertTrue( $this->ruleDailySimple->isSatisfied(array()) );
		
		// the rule should not satisfy for today
		$lastRuns = new Majisti_Date();
		$this->assertFalse( $this->ruleDailySimple->isSatisfied( $lastRuns ) );
		
	}
	
	/**
	 * Test whether a task can be run tomorrow with no restriction
	 * or interval
	 */
	public function testTomorrowSimple() {
		
		$tomorrow = new Majisti_Date();
		$tomorrow->addDay(1);
		$this->ruleDailySimple->setTime( $tomorrow );
		
		// the rule should satisfy with no last runs
		$this->assertTrue( $this->ruleDailySimple->isSatisfied(null) );
		$this->assertTrue( $this->ruleDailySimple->isSatisfied(array()) );
		
		// the rule should be satisfied tomorrow
		$lastRuns = new Majisti_Date();
		$this->assertTrue( $this->ruleDailySimple->isSatisfied($lastRuns) );
		
	}
	
	/**
	 * Test whether a task can be run next week with no restriction
	 * or interval
	 */
	public function testNextWeekSimple() {
		
		$nextWeek = new Majisti_Date();
		$nextWeek->addWeek(1);
		$this->ruleWeeklySimple->setTime( $nextWeek );
		
		// the rule should satisfy with no last runs
		$this->assertTrue( $this->ruleWeeklySimple->isSatisfied(null) );
		$this->assertTrue( $this->ruleWeeklySimple->isSatisfied(array()) );
		
		// the rule should be satisfied tomorrow
		$lastRuns = new Majisti_Date();
		$this->assertTrue( $this->ruleWeeklySimple->isSatisfied($lastRuns) );
		
	}
	
	/**
	 * Test whether a task can be run next month with no restriction
	 * or interval
	 */
	public function testNextMonthSimple() {
		
		$nextMonth = new Majisti_Date();
		$nextMonth->addMonth(1);
		$this->ruleMonthlySimple->setTime( $nextMonth );
		
		// the rule should satisfy with no last runs
		$this->assertTrue( $this->ruleMonthlySimple->isSatisfied(null) );
		$this->assertTrue( $this->ruleMonthlySimple->isSatisfied(array()) );
		
		// the rule should be satisfied tomorrow
		$lastRuns = new Majisti_Date();
		$this->assertTrue( $this->ruleMonthlySimple->isSatisfied($lastRuns) );
		
	}
	
	/**
	 * Test whether a task can be run next year with no restriction
	 * or interval
	 */
	public function testNextYearSimple() {
		
		$nextYear = new Majisti_Date();
		$nextYear->addYear(1);
		$this->ruleYearlySimple->setTime( $nextYear );
		
		// the rule should satisfy with no last runs
		$this->assertTrue( $this->ruleYearlySimple->isSatisfied(null) );
		$this->assertTrue( $this->ruleYearlySimple->isSatisfied(array()) );
		
		// the rule should be satisfied tomorrow
		$lastRuns = new Majisti_Date();
		$this->assertTrue( $this->ruleYearlySimple->isSatisfied($lastRuns) );
		
	}
	
	/**
	 * Test the next allowed run with an interval
	 */
	public function testDayInterval() {
		
		$this->ruleDailySimple->setOptions(array(
			'interval' => self::INTERVAL_DAILY
		));
		
		$time = new Majisti_Date('2009-01-01');
		$lastRuns = new Majisti_Date('2009-01-01');

		$loopCount = 0;
		$this->ruleDailySimple->setTime( $time );
		while ( !$this->ruleDailySimple->isSatisfied($lastRuns) ) {
			// the rule should satisfy with no last runs anyway
			$this->assertTrue( $this->ruleDailySimple->isSatisfied(null) );
			$this->assertTrue( $this->ruleDailySimple->isSatisfied(array()) );
			
			$loopCount++;
			$time->addDay(1);
			$this->ruleDailySimple->setTime( $time );
		}
		
		// the number of loop counts - 1 (the number of days in the interval)
		// should match the actual interval
		$this->assertEquals( self::INTERVAL_DAILY, $loopCount );
		
	}

	/**
	 * Test the next allowed run with an interval
	 */
	public function testWeekInterval() {
		
		$this->ruleWeeklySimple->setOptions(array(
			'interval' => self::INTERVAL_WEEKLY
		));
		
		$time = new Majisti_Date('2009-01-01');
		$lastRuns = new Majisti_Date('2009-01-01');

		$loopCount = 0;
		$this->ruleWeeklySimple->setTime( $time );
		while ( !$this->ruleWeeklySimple->isSatisfied($lastRuns) ) {
			// the rule should satisfy with no last runs anyway
			$this->assertTrue( $this->ruleWeeklySimple->isSatisfied(null) );
			$this->assertTrue( $this->ruleWeeklySimple->isSatisfied(array()) );
			
			$loopCount++;
			$time->addWeek(1);
			$this->ruleWeeklySimple->setTime( $time );
		}
		
		// the number of loop counts - 1 (the number of months in the interval)
		// should match the actual interval
		$this->assertEquals( self::INTERVAL_WEEKLY, $loopCount );
		
	}
	
	/**
	 * Test the next allowed run with an interval
	 */
	public function testMonthInterval() {
		
		$this->ruleMonthlySimple->setOptions(array(
			'interval' => self::INTERVAL_MONTHLY
		));
		
		$time = new Majisti_Date('2009-01-01');
		$lastRuns = new Majisti_Date('2009-01-01');

		$loopCount = 0;
		$this->ruleMonthlySimple->setTime( $time );
		while ( !$this->ruleMonthlySimple->isSatisfied($lastRuns) ) {
			// the rule should satisfy with no last runs anyway
			$this->assertTrue( $this->ruleMonthlySimple->isSatisfied(null) );
			$this->assertTrue( $this->ruleMonthlySimple->isSatisfied(array()) );
			
			$loopCount++;
			$time->addMonth(1);
			$this->ruleMonthlySimple->setTime( $time );
		}
		
		// the number of loop counts - 1 (the number of months in the interval)
		// should match the actual interval
		$this->assertEquals( self::INTERVAL_MONTHLY, $loopCount );
		
	}
		
	/**
	 * Test the next allowed run with an interval
	 */
	public function testYearInterval() {
		
		$this->ruleYearlySimple->setOptions(array(
			'interval' => self::INTERVAL_YEARLY
		));
		
		$time = new Majisti_Date('2009-01-01');
		$lastRuns = new Majisti_Date('2009-01-01');

		$loopCount = 0;
		$this->ruleYearlySimple->setTime( $time );
		while ( !$this->ruleYearlySimple->isSatisfied($lastRuns) ) {
			// the rule should satisfy with no last runs anyway
			$this->assertTrue( $this->ruleYearlySimple->isSatisfied(null) );
			$this->assertTrue( $this->ruleYearlySimple->isSatisfied(array()) );
			
			$loopCount++;
			$time->addYear(1);
			$this->ruleYearlySimple->setTime( $time );
		}
		
		// the number of loop counts - 1 (the number of months in the interval)
		// should match the actual interval
		$this->assertEquals( self::INTERVAL_YEARLY, $loopCount );
		
	}
	
	/**
	 * Test adding a single constraint, then removing it
	 */
	public function testAddConstraint() {
		
		// no constraint set
		$this->assertEquals(0, count($this->ruleDailySimple->getConstraints()) );
		
		$constraint = new Majisti_Date();
		
		$this->ruleDailySimple->addConstraint( $constraint );
		$this->assertEquals(1, count($this->ruleDailySimple->getConstraints()) );
		
		// no duplicate
		$this->ruleDailySimple->addConstraint( $constraint );
		$this->assertEquals(1, count($this->ruleDailySimple->getConstraints()) );
		
		$this->ruleDailySimple->removeConstraint( new Majisti_Date() );
		$this->assertEquals(0, count($this->ruleDailySimple->getConstraints()) );
		
		// add more constraints
		$constraints = array();
		$time = new Majisti_Date();
		for ($i=0; $i<self::CONSTRAINT_COUNT; $i++) {
			$constraints[] = clone $time;
			$time->addDay(1);
		}
		
		$this->ruleYearlySimple->setConstraints($constraints);
		$this->assertEquals( self::CONSTRAINT_COUNT, count($this->ruleYearlySimple->getConstraints()) );
		$this->ruleYearlySimple->removeConstraints($constraints);
		$this->assertEquals( 0, count($this->ruleYearlySimple->getConstraints()) );
		
	}
	
	/**
	 * Test the basic constraints on each rule types
	 */
	public function testConstraints() {
	
		$lastRuns = array(
			new Majisti_Date('2009-01-05')
		);
		
		$constraints = array(
			array('year' => 2009, 'month' => 1, 'day' => 8)
		);
		
		$time = new Majisti_Date('2009-01-06');
		
		// TODO : add more constraints to test every possible situations

		// *** TEST 1 ***
		$this->ruleDailySimple->setTime( $time );
		$this->assertTrue( $this->ruleDailySimple->isSatisfied($lastRuns) );

		$this->ruleWeeklySimple->setTime( $time );
		// the test can be executed once more this week (the 8th)
		$this->assertFalse( $this->ruleWeeklySimple->isSatisfied($lastRuns) );
		
		$this->ruleMonthlySimple->setTime( $time );
		// the test can be executed once more this week (the 8th)
		$this->assertFalse( $this->ruleMonthlySimple->isSatisfied($lastRuns) );
		
		$this->ruleYearlySimple->setTime( $time );
		// the test can be executed once more this week (the 8th)
		$this->assertFalse( $this->ruleYearlySimple->isSatisfied($lastRuns) );
		
		// *** TEST 2 *** repeat the same tests, but with one constraint this time
		$this->ruleDailySimple->setConstraints( $constraints );
		$this->ruleDailySimple->setTime( $time );
		$this->assertTrue( $this->ruleDailySimple->isSatisfied($lastRuns) );

		$this->ruleWeeklySimple->setConstraints( $constraints );
		$this->ruleWeeklySimple->setTime( $time );
		// the test can be executed once more this week (the 8th)
		$this->assertTrue( $this->ruleWeeklySimple->isSatisfied($lastRuns) );
		
		$this->ruleMonthlySimple->setConstraints( $constraints );
		$this->ruleMonthlySimple->setTime( $time );
		// the test can be executed once more this week (the 8th)
		$this->assertTrue( $this->ruleMonthlySimple->isSatisfied($lastRuns) );
		
		$this->ruleYearlySimple->setConstraints( $constraints );
		$this->ruleYearlySimple->setTime( $time );
		// the test can be executed once more this week (the 8th)
		$this->assertTrue( $this->ruleYearlySimple->isSatisfied($lastRuns) );
		
	}
	
	/**
	 * Test the last run value given simple constraints
	 */
	public function testLastRun() {
		
		$lastRuns = array(
			new Majisti_Date('2009-01-05')
		);
		
		$constraints = array(
			array('year' => 2010, 'month' => 1, 'day' => 8)
		);
		
		$time = new Majisti_Date('2010-01-10');
		
		$this->ruleDailySimple->setConstraints( $constraints );
		$this->ruleDailySimple->setTime( $time );
		$this->assertEquals( new Majisti_Date('2010-01-08'), $this->ruleDailySimple->getLastLogicalRun($lastRuns) );
		$this->ruleDailySimple->setTime( new Majisti_Date('2009-01-08') );
		$this->assertEquals( new Majisti_Date('2009-01-07 23:59:59'), $this->ruleDailySimple->getLastLogicalRun($lastRuns) );
		$this->ruleDailySimple->setTime( new Majisti_Date('2009-01-20') );
		$this->assertEquals( new Majisti_Date('2009-01-19 23:59:59'), $this->ruleDailySimple->getLastLogicalRun($lastRuns) );
		$this->ruleDailySimple->setTime( new Majisti_Date('2009-02-05') );
		$this->assertEquals( new Majisti_Date('2009-02-04 23:59:59'), $this->ruleDailySimple->getLastLogicalRun($lastRuns) );
		$this->ruleDailySimple->setTime( new Majisti_Date('2010-01-05') );
		$this->assertEquals( new Majisti_Date('2010-01-04 23:59:59'), $this->ruleDailySimple->getLastLogicalRun($lastRuns) );
		$this->ruleDailySimple->setTime( new Majisti_Date('2010-01-10') );
		$this->assertEquals( new Majisti_Date('2010-01-08'), $this->ruleDailySimple->getLastLogicalRun($lastRuns) );
		
		$this->ruleWeeklySimple->setConstraints( $constraints );
		$this->ruleWeeklySimple->setTime( $time );
		$this->assertEquals( new Majisti_Date('2010-01-08'), $this->ruleWeeklySimple->getLastLogicalRun($lastRuns) );
		$this->ruleWeeklySimple->setTime( new Majisti_Date('2009-01-10') );
		$this->assertEquals( new Majisti_Date('2009-01-03 23:59:59'), $this->ruleWeeklySimple->getLastLogicalRun($lastRuns) );
		$this->ruleWeeklySimple->setTime( new Majisti_Date('2009-01-20') );
		$this->assertEquals( new Majisti_Date('2009-01-17 23:59:59'), $this->ruleWeeklySimple->getLastLogicalRun($lastRuns) );
		$this->ruleWeeklySimple->setTime( new Majisti_Date('2009-02-05') );
		$this->assertEquals( new Majisti_Date('2009-01-31 23:59:59'), $this->ruleWeeklySimple->getLastLogicalRun($lastRuns) );
		$this->ruleWeeklySimple->setTime( new Majisti_Date('2010-01-05') );
		$this->assertEquals( new Majisti_Date('2010-01-02 23:59:59'), $this->ruleWeeklySimple->getLastLogicalRun($lastRuns) );
		$this->ruleWeeklySimple->setTime( new Majisti_Date('2010-01-10') );
		$this->assertEquals( new Majisti_Date('2010-01-08'), $this->ruleWeeklySimple->getLastLogicalRun($lastRuns) );
		
		$this->ruleMonthlySimple->setConstraints( $constraints );
		$this->ruleMonthlySimple->setTime( $time );
		$this->assertEquals( new Majisti_Date('2010-01-08'), $this->ruleMonthlySimple->getLastLogicalRun($lastRuns) );
		$this->ruleMonthlySimple->setTime( new Majisti_Date('2009-01-10') );
		$this->assertEquals( new Majisti_Date('2008-12-31 23:59:59'), $this->ruleMonthlySimple->getLastLogicalRun($lastRuns) );
		$this->ruleMonthlySimple->setTime( new Majisti_Date('2009-01-20') );
		$this->assertEquals( new Majisti_Date('2008-12-31 23:59:59'), $this->ruleMonthlySimple->getLastLogicalRun($lastRuns) );
		$this->ruleMonthlySimple->setTime( new Majisti_Date('2009-02-05') );
		$this->assertEquals( new Majisti_Date('2009-01-31 23:59:59'), $this->ruleMonthlySimple->getLastLogicalRun($lastRuns) );
		$this->ruleMonthlySimple->setTime( new Majisti_Date('2010-01-05') );
		$this->assertEquals( new Majisti_Date('2009-12-31 23:59:59'), $this->ruleMonthlySimple->getLastLogicalRun($lastRuns) );
		$this->ruleMonthlySimple->setTime( new Majisti_Date('2010-01-10') );
		$this->assertEquals( new Majisti_Date('2010-01-08'), $this->ruleMonthlySimple->getLastLogicalRun($lastRuns) );
		
		$this->ruleYearlySimple->setConstraints( $constraints );
		$this->ruleYearlySimple->setTime( $time );
		$this->assertEquals( new Majisti_Date('2010-01-08'), $this->ruleYearlySimple->getLastLogicalRun($lastRuns) );
		$this->ruleYearlySimple->setTime( new Majisti_Date('2009-01-10') );
		$this->assertEquals( new Majisti_Date('2008-12-31 23:59:59'), $this->ruleYearlySimple->getLastLogicalRun($lastRuns) );
		$this->ruleYearlySimple->setTime( new Majisti_Date('2009-01-20') );
		$this->assertEquals( new Majisti_Date('2008-12-31 23:59:59'), $this->ruleYearlySimple->getLastLogicalRun($lastRuns) );
		$this->ruleYearlySimple->setTime( new Majisti_Date('2009-02-05') );
		$this->assertEquals( new Majisti_Date('2008-12-31 23:59:59'), $this->ruleYearlySimple->getLastLogicalRun($lastRuns) );
		$this->ruleYearlySimple->setTime( new Majisti_Date('2010-01-05') );
		$this->assertEquals( new Majisti_Date('2009-12-31 23:59:59'), $this->ruleYearlySimple->getLastLogicalRun($lastRuns) );
		$this->ruleYearlySimple->setTime( new Majisti_Date('2010-01-10') );
		$this->assertEquals( new Majisti_Date('2010-01-08'), $this->ruleYearlySimple->getLastLogicalRun($lastRuns) );
		
	}
	
	/**
	 * Test the next runs for a weekly basis
	 */
	public function testNextRunWeek() {

		$lastRuns = array(
			new Majisti_Date('2009-06-03')  // wednesday, June 3, 2009
		);
		
		$constraints = array(
			array('weekday_digit' => 2),  // Tuesday
			array('weekday_digit' => 3),  // Wednesday
			array('weekday_digit' => 5)   // Saturday
		);
		
		$this->ruleWeeklySimple->setConstraints($constraints);
		
		$this->ruleWeeklySimple->setTime( new Majisti_Date('2009-06-01') );
		
		foreach ($constraints as $c) {
			$nextRun = $this->ruleWeeklySimple->getNextLogicalRun();
			$this->assertEquals($c['weekday_digit'], $nextRun->get('WEEKDAY_DIGIT'));
			$this->ruleWeeklySimple->setTime( $nextRun );
		}
		
		// there is still one day in the constraints left to run this week...
		$this->ruleWeeklySimple->setTime( new Majisti_Date('2009-06-04') );
		$this->assertTrue( $this->ruleWeeklySimple->isSatisfied( $lastRuns ) );

		// we can still run because last run was before the 5th day...
		$this->ruleWeeklySimple->setTime( new Majisti_Date('2009-06-06') );
		$this->assertTrue( $this->ruleWeeklySimple->isSatisfied( $lastRuns ) );
		
		$lastRuns[] = new Majisti_Date('2009-06-05'); // add a last run and the 5th day...
		// we can't run anymore
		$this->assertFalse( $this->ruleWeeklySimple->isSatisfied( $lastRuns ) );
		
	}
	
	public function testStartTime() {
		
		$this->ruleMonthlySimple->setOptions(array(
			'start' => new Majisti_Date('2009-06-05')
		));
		
		$time = new Majisti_Date('2009-01-01');
		
		for ($i=1; $i<6; $i++) {
			$time->setMonth($i);
			$this->ruleMonthlySimple->setTime($time);
			$this->assertFalse( $this->ruleMonthlySimple->isSatisfied(null) );
		}
		
		$time->setMonth(6);  // the date is still before june 5th...
		$this->ruleMonthlySimple->setTime($time);
		$this->assertFalse( $this->ruleMonthlySimple->isSatisfied(null) );
		
		$this->ruleMonthlySimple->setTime( new Majisti_Date('2009-06-05') );
		$this->assertTrue( $this->ruleMonthlySimple->isSatisfied(null) );
		
		$time->setMonth(7);  // after the starting date
		$this->ruleMonthlySimple->setTime($time);
		$this->assertTrue( $this->ruleMonthlySimple->isSatisfied(null) );
		
	}
	
	public function testExpireTime() {

		$this->ruleMonthlySimple->setOptions(array(
			'expire' => new Majisti_Date('2009-06-05')
		));
		
		$time = new Majisti_Date('2009-01-01');
		
		for ($i=1; $i<6; $i++) {
			$time->setMonth($i);
			$this->ruleMonthlySimple->setTime($time);
			$this->assertTrue( $this->ruleMonthlySimple->isSatisfied(null) );
		}
		
		$time->setMonth(6);  // the date is still before june 5th...
		$this->ruleMonthlySimple->setTime($time);
		$this->assertTrue( $this->ruleMonthlySimple->isSatisfied(null) );
		
		// the expire date is the same, but we're still ok...
		$this->ruleMonthlySimple->setTime( new Majisti_Date('2009-06-05') );
		$this->assertTrue( $this->ruleMonthlySimple->isSatisfied(null) );
		
		// NOTE : we must specify a last run because, if we don't, we can
		//        still execute the task on time of the expiration date
		$lastRuns = array(
			new Majisti_Date('2009-06-05')
		);
		$this->ruleMonthlySimple->setTime( new Majisti_Date('2009-06-30') );
		$this->assertFalse( $this->ruleMonthlySimple->isSatisfied($lastRuns) );
		
		// repeat the same, but the last run will not be exactly on the expiration time,
		// but in the same time interval
		$lastRuns = array(
			new Majisti_Date('2009-06-05')
		);
		$this->ruleMonthlySimple->setOptions(array(
			'expire' => null
		));
		$this->ruleMonthlySimple->setTime( new Majisti_Date('2009-06-30') );
		$this->assertFalse( $this->ruleMonthlySimple->isSatisfied($lastRuns) );
				
	}

}

Majisti_Test_Runner::run('Majisti_Scheduler_Task_RuleTest');
