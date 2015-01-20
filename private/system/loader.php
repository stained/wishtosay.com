<?php

ini_set('default_charset', 'UTF-8');

class CustomLoader
{
    private $loader;

    public function __construct($loader)
    {
        $this->loader = $loader;
    }

    public function loadClass($class)
    {
        // determine script path
        $scriptPath = $_SERVER['SCRIPT_FILENAME'];

        $path = strtolower('../private/' .
            str_replace("\\", "/", $class) . '.php');

        if(file_exists($path))
        {
            // load file
            require_once($path);
            return;
        }

        $result = $this->loader->loadClass($class);

        if ($result && method_exists($class, '__static')) {
            call_user_func(array($class, '__static'));
        }

        return $result;
    }
}

$loader = require '../private/vendor/autoload.php';
$loader->unregister();

spl_autoload_register(array(new CustomLoader($loader), 'loadClass'));

