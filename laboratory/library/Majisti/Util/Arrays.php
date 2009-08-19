<?php

namespace Majisti\Util;

class Arrays
{
    public static function shuffle($array, $preserveKeys = false)
    {
        if( !$preserveKeys ) {
            shuffle($array);
            return $array;    
        }
        
        $arrayKeys = array_keys($array);
        shuffle($arrayKeys);
        
        $randomizedArray = array();
        
        foreach ($arrayKeys as $arrayKey) {
            $randomizedArray[$arrayKey] = $array[$arrayKey];
        }
        
        return $randomizedArray;    
    }
    
    /**
     * @desc Extract every $key from each item and return an array
     * of those values.
     * 
     * Ex:
     * 
     * $arr = array( array('foo' => 1, 'bar' => 2), array('foo' => 10, 'bar' => 11) )
     * 
     * Majisti_Util_Array::pluck('foo', $arr);   // returns array(1, 10);   
     * Majisti_Util_Array::pluck('bar', $arr);   // returns array(2, 11);
     *
     * It also works with numeric indexes.
     * 
     * If $array is a 1-dimensional array, an empty array is returned.
     * If $key is an array, or $array is not an array, an empty array is returned
     * 
     * If an item does not contain the specified $key, NULL is set for that index
     * 
     * Ex:
     * 
     * $arr = array( array('foo' => 1, 'bar' => 2), array('bar' => 11) )
     * 
     * Majisti_Util_Array::pluck('foo', $arr);   // returns array(1, NULL);
     * 
     * @param string|int $key
     * @param array $array
     * @return array
     */    
    public static function pluck($key, $array) {
        if (is_array($key) || !is_array($array)) return array();
        $funct = create_function('$e', 'return is_array($e) && array_key_exists("'.$key.'",$e) ? $e["'. $key .'"] : null;');
        return array_map($funct, $array);
    }
}