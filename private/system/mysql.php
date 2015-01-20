<?php

namespace System;

use \Dabble\Database;
use \System\Config;

class Mysql {

    /**
     * @var Database
     */
    private static $instance;

    public static function getInstance()
    {
        if (!empty(static::$instance))
        {
            return static::$instance;
        }

        $mysqlConfig = Config::get('mysql');

        static::$instance = new Database($mysqlConfig['server'],
                                         $mysqlConfig['user'],
                                         $mysqlConfig['password'],
                                         $mysqlConfig['database'],
                                         $mysqlConfig['charset'],
                                         $mysqlConfig['port']
        );

        return static::$instance;
    }
}