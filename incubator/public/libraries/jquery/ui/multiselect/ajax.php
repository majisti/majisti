<?php

session_start();


/***************************************************************************
*
*   Author   : Eric Sizemore ( www.secondversion.com & www.phpsociety.com )
*   Package  : Random Word
*   Version  : 1.0.1
*   Copyright: (C) 2006 - 2007 Eric Sizemore
*   Site     : www.secondversion.com & www.phpsociety.com
*   Email    : esizemore05@gmail.com
*
*   Modified by: Yanick Rochon (yanick.rochon@gmail.com)
*
*   This program is free software; you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation; either version 2 of the License, or
*   (at your option) any later version.
*
*   This program is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
*   GNU General Public License for more details.
*
***************************************************************************/

// Slightly inspired by class randomWord by kumar mcmillan
class Randomword
{
    static private $vowels = array('a','e','i','o','u','y','oi','eu','ay','ey','io');
    static private $consonants = array('b','c','d','f','g','h','j','k','l','m','n','p','r','s','t','v','w','z','ch','qu','th','xy','sh','ph','sk');

    /**
    * Constructor.
    *
    * @param  integer  Length of the word
    * @param  boolean  Return the word lowercase?
    * @param  boolean  Reutrn the word with the first letter capitalized?
    * @param  boolean  Return the word uppercase?
    * @return string
    */
    static public function get($length = 5, $lower_case = true, $ucfirst = false, $upper_case = false)
    {
        $lastVowel = mt_rand(0, 100) > 50; // start with a vowel or consonant...
        $word = '';

        while (strlen($word) < $length)
        {
            if ($lastVowel) {
                $word .= self::$consonants[array_rand(self::$consonants)];
            } else {
                $word .= self::$vowels[array_rand(self::$vowels)];
            }
            $lastVowel = !$lastVowel;
        }

        $word = substr($word, 0, $length);

        if ($lower_case) {
            $word = strtolower($word);
        }
        else if ($ucfirst) {
            $word = ucfirst(strtolower($word));
        }
        else if ($upper_case) {
            $word = strtoupper($word);
        }
        return $word;
    }
}


if ( !isset($_SESSION['data']) ) {

  $data = array();

  $start = abs((microtime(true)*1000) & 0xffffffff);

  for ($i=1; $i<=1000; $i++) {
    $value = $start + $i;
    
    $data[$value] = Randomword::get(rand(4, 12));
  }

  $_SESSION['data'] = $data;
}

header('Content-type: text/plain');

if ( isset($_GET['q'] ) ) {

  $data = $_SESSION['data'];
  
  $count = 0;
  foreach ($data as $value => $word) {
    if ( stripos($word, $_GET['q']) === 0 ) {
      echo $value . '=' . $word . "\n";
      if ( $count++ > 10 ) break;
    }
  }

}

