<?php namespace Util;

class Arr
{
    /**
     * Get value from array
     * 
     * @param array $array
     * @param mixed $fields pass in array for multi-dimensional fields
     * @param mixed $default
     * @return mixed
     */
    public static function get($array, $fields, $default = null)
    {
        if(empty($array) || empty($fields))
        {
            return $default;
        }
        
        if(!is_array($fields))
        {
            if(!array_key_exists($fields, $array))
            {
                return $default;
            }
            
            return $array[$fields];
        }
        
        // try to get all dimensions of the array
        foreach($fields as $dimension)
        {
            if(!is_array($array) || !array_key_exists($dimension, $array))
            {
                return $default;
            }
            
            $array = $array[$dimension];
        }
        
        return $array;
    }
    
    
}

