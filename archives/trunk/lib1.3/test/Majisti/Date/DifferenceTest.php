<?php

require_once dirname(__FILE__) . '/../../TestHelper.php';

class Majisti_Date_DifferenceTest extends Majisti_Test_PHPUnit_TestCase
{
	public function testGet()
	{
		$dateSrc 	= new Majisti_Date('2000-02-02');
		$dateTg 	= new Majisti_Date('2000-02-20');
		$diff 		= new Majisti_Date_Difference($dateSrc, $dateTg);
		
		$this->assertEquals(18, $diff->get(Majisti_Date::DAY));
		$this->assertEquals(432, $diff->get(Majisti_Date::HOUR));
		$this->assertEquals(25920, $diff->get(Majisti_Date::MINUTE));
		$this->assertEquals(1555200, $diff->get(Majisti_Date::SECOND));
		$this->assertEquals(1555200000, $diff->get(Majisti_Date::MILLISECOND));
	}
}

Majisti_Test_Runner::run('Majisti_Date_DifferenceTest');
