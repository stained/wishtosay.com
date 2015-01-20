<?php
echo "Importing locations\n";

// load list of countries
$countries = array();

$db = \System\Mysql::getInstance();
$db->truncate('country');

if (($handle = fopen("../database/countries.csv", "r")) !== FALSE)
{
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $data = array_map("utf8_encode", $data);

        $country = null;

        $code = $data[1];

        if(!isset($countries[$code]))
        {
            $country = \Model\Country::getByCode($code);

            if(empty($country))
            {
                $country = \Model\Country::createCountry($data[0], $data[1]);
            }

            $countries[$code] = $country;
        }
    }

    fclose($handle);
}

$searchIndex = $elasticSearchConfig['index'];

$params = array('index'=>$searchIndex, 'ignore'=>array(404, 400));
$client->indices()->delete($params);
$client->indices()->create($params);

// remove from mysql
$db->truncate('location');

// parse each city by linking to country
if (($handle = fopen("../database/geoip_city.csv", "r")) !== FALSE)
{
    $index = 0;

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $data = array_map("utf8_encode", $data);

        if(++$index >= 3)
        {
            if (empty($data[1]) || empty($data[3]))
            {
                continue;
            }

            $code = $data[1];

            if(isset($countries[$code])) {
                $country = $countries[$code];

                // insert into mysql
                $location = \Model\Location::createLocation(
                    $country,
                    $data[3],
                    floatval($data[5]),
                    floatval($data[6])
                );

                if(!empty($location)) {
                    $searchBody = array(
                        'country' => $country->getCountry(),
                        'city' => $data[3],
                        'pin' => array(
                            'location' => array(
                                'lat' => floatval($data[5]),
                                'lon' => floatval($data[6])
                            )
                        )
                    );

                    // insert into elastic search
                    $params = array(
                        'body' => $searchBody,
                        'index' => $searchIndex,
                        'type' => 'location',
                        'id' => $location->getId(),
                        'timestamp' => time()
                    );

                    $client->index($params);
                }
            }
        }
    }

    fclose($handle);
}

echo "Done importing locations\n";
