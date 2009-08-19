<?php

/*
 * Test helper
 */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'TestHelper.php';

/**
 * Test the Majisti_Ads class
 *
 * @lastmodified 2009-03-07
 *
 * @author Yanick Rochon
 * @version 1
 */
class Majisti_AdsTest extends Majisti_Test_PHPUnit_TestCase
{

	const LOOP_COUNT = 10000;
	
	const LOOP_ERR_MARGIN = 0.01;   // allow only 1% of errors in the normal distribution
	
	/**
	 * The Majisti_Ads instance used throughout the tests
	 *
	 * @var Majisti_Ads
	 */
	private $ads;
	/**
	 * The mock backend used to load mock Banners
	 *
	 * @var Majisti_Ads_Backend_Abstract
	 */
	private $backend;


	public function setUp() {
		$this->backend = new Majisti_Ads_Backend_Mock();
		$this->ads = new Majisti_Ads('Ads_Test', $this->backend);
	}

	/**
	 * Get random banners. The test assumes that :
	 * 
	 *   1. there are at least two banners in the set
	 * 
	 * It will fail if :
	 *  
	 *   1. two same banners are returned twice in a row 
	 *   2. a null banner is returned
	 *   2. the returned banners do not follow a normal distribution
	 *      with an error margin of at most self::LOOP_ERR_MARGIN 
	 *
	 */
	public function testGetRandomBanner() {
		$returnedBanners = array();
		$returnedBannersCount = array();
		$lastBanner = null;
		
		// test LOOP_COUNT times the function getRandomBanner() and collect statistics
		for ($i=0; $i < self::LOOP_COUNT; $i++) {
			$banner = $this->ads->getRandomBanner();
			
			if ( ($index = array_search($banner, $returnedBanners, true)) === FALSE ) {
				$returnedBanners[] = $banner;
				$returnedBannersCount[] = 1;
			} else {
				$returnedBannersCount[$index]++;
			}
			
			if ( $banner === null ) {
				$this->fail('random banner is null (iteration ' . $i . ')');
			} else if ( $banner === $lastBanner ) {
				$this->fail('random banner duplicate (iteration ' . $i . ')');
			}
		}

		$weightSum = 0;
		foreach ($this->ads as $ad) {
			$weightSum += $ad->getWeight();
		}
		
		foreach ($returnedBanners as $index => $banner) {
			$avgWeight = round($banner->getWeight() / $weightSum, 3);
			$avg = round($returnedBannersCount[$index] / self::LOOP_COUNT, 3);
			$diff = abs($avgWeight - $avg);
			//echo "Banner " . $banner->getName() . ", weight " . $banner->getWeight() . " appeared " . $returnedBannersCount[$index] . " times = " . $avg . ' - ' . $avgWeight . ' = ' . $diff . "\n";
			
			// we must satisfy the margin of error
			if ( $diff > self::LOOP_ERR_MARGIN ) {
				$this->fail('distribution above accepted tolerances : ' . $diff . ' > ' . self::LOOP_ERR_MARGIN);
			}
		}
		
	}
	
}

class Majisti_Ads_Backend_Mock extends Majisti_Ads_Backend_Abstract 
{
	const BANNERS_COUNT = 20;
	const WEIGHT_FLOAT_DIGITS = 4;
	const WEIGHT_MAX = 30;
	
	public function getNewId() {
		return null;
	}
	
	public function isValidId($id) {
		return false;
	}
	
	public function load() {
		$banners = array();
		$rand_div = pow(10, self::WEIGHT_FLOAT_DIGITS);
		$rand_max = self::WEIGHT_MAX * $rand_div;
		
		for ($i = 0; $i < self::BANNERS_COUNT; $i++) {
			$weight = mt_rand(0, $rand_max) / $rand_div;
			$banners[$i] = new Majisti_Ads_Banner_Mock(array('name' => $i, 'weight' => $weight));
		}		
		return $banners;
	}
	
	public function save($data) {
		/* nothing */
	}
}

class Majisti_Ads_Banner_Mock extends Majisti_Ads_Banner_Abstract {
	public function toString() {
		return '<a href="">Test banner #' . $this->getName() . ', weight = ' . $this->getWeight() . '</a>';
	}
}



Majisti_Test_Runner::run('Majisti_AdsTest');
