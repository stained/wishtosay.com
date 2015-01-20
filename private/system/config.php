<?php namespace System;

use \Util\Arr;

class Config
{
    private static $config;
    private static $current;
    
    /**
     * get the value of a specific config setting
     * 
     * @param array $keys
     * @param string $config
     * @return mixed
     */
    public static function get($keys, $config = 'system')
    {
        if(!empty($keys))
        {
            if(empty(self::$config))
            {
                self::$config = require_once("../private/config/{$config}.cfg");
                self::$current = self::$config['current'];
            }
            
            // check override
            $value = Arr::get(self::$config, $keys);
            
            if(empty($value))
            {
                // look for server-specific value
                $value = Arr::get(self::$config[self::$current], $keys);
            }
            
            return $value;
        }
        
        return null;
    }
 
}

