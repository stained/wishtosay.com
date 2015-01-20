<?php

// require autoloader
require_once('../private/system/loader.php');


// remove index from elastic search
$elasticSearchConfig = \System\Config::get('elasticsearch');
$params = array();
$params['hosts'] = $elasticSearchConfig['hosts'];
$client = new \Elasticsearch\Client($params);

$searchIndex = $elasticSearchConfig['index'];

$params = array('index'=>$searchIndex, 'ignore'=>array(404, 400));
$client->indices()->delete($params);
$client->indices()->create($params);

include('import_locations.php');
include('import_gender_tags.php');

echo "Done importing\n";
