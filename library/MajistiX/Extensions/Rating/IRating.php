<?php

namespace MajistiX\Rating;

/**
 * @desc This is the interface that will identify a concrete rating.
 * Rating consists of having three distinct types: A rater, a rated
 * and a rating. A 'rater' is usually who rated the 'rated' object
 * by applying a 'rating' on it. All of these can then be used
 * to calculate an average rating and thus could be used in a star
 * rating system for example.
 * 
 * This interface gives the responsability to the concrete instance
 * to implement it's own internal storage for the rating.
 *
 * @author Majisti
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
interface IRating
{
	/**
	 * @desc Rates a specified rated object by applying a rating on it.
	 * @return true if the vote was cast correctly.
	 */
	public function rate($rater, $rated, $rating);
	
	/**
	 * @return boolean True, if the rater has already rated on a given 'rated' object
	 */
	public function hasRated($rater, $rated);
	
	/**
	 * @return float The rating's average for a rated object. Same as dividing totalRating by totalVotes.
	 */
	public function getAverageRating($rated);
	
	/**
	 * @return integer The scale used for rating
	 */
	public function getScale();
	
	/**
	 * @desc Returns the rating of a rater.
	 *
	 * @param integer $rater The rater's ID
	 * @param integer $rated The rated object's ID
	 * @return int The rater's rating or -1 if the rater has never rated.
	 */
	public function getRating($rater, $rated);
	
	/**
	 * @return integer The total rating for a particular rated object
	 */
	public function getTotalRating($rated);
	
	/**
	 * @return integer the total number of votes on a particular rated object
	 */
	public function getTotalVotes($rated);
}
