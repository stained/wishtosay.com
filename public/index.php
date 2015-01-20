<?php
/*
 * index.php
 * 
 * Entry point from the web
 * 
 */

// require autoloader
require_once('../private/system/loader.php');

// let the router handle the routing
\System\Router::route()->display();
