<?php
echo "Importing locations\n";

// load list of countries
$countries = array();

$db = \System\Mysql::getInstance();

$db->truncate('continent');
$db->truncate('country');
$db->truncate('subdivision');
$db->truncate('city');

$continents = array();
$countries = array();
$subdivisions = array();
$cities = array();
$cityLatLons = array();

if (($handle = fopen("../database/geoip_city.csv", "r")) !== FALSE) {
    $index = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        if ($index == 0) {
            $index = 1;
            continue;
        }

        $countryCode = $data[1];
        $cityName = $data[3];
        $latLon = array('lat'=>$data[5], 'lon'=>$data[6]);

        $testCode = $countryCode . "_" . $cityName;
        $cityLatLons[$testCode] = $latLon;
    }
}

if (($handle = fopen("../database/geolitelocations.csv", "r")) !== FALSE)
{
    $index = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        if($index == 0)
        {
            $index = 1;
            continue;
        }

        $data = array_map("utf8_encode", $data);

        $continentCode = $data[2];
        $continentName = $data[3];
        $countryCode = $data[4];
        $countryName = $data[5];
        $subdivision1Code = $data[6];
        $subdivision1Name = $data[7];
        $subdivision2Code = $data[8];
        $subdivision2Name = $data[9];
        $cityName = $data[10];

        /*
        echo $continentCode . ", " .
             $continentName . ", " .
            $countryCode . ", " .
            $countryName . ", " .
            $subdivision1Code . ", " .
            $subdivision1Name . ", " .
            $cityName . "\n";
        */

        $continent = null;
        $country = null;
        $subdivision = null;
        $city = null;

        // check if continent exists
        if (isset($continents[$continentCode]))
        {
            $continent = $continents[$continentCode];
        }
        else
        {
            $continent = \Model\Continent::createContinent($continentName, $continentCode);
            $continents[$continentCode] = $continent;
        }

        // check if country exists
        $testCode = $continentCode . "_" . $countryCode;
        if (isset($countries[$testCode]))
        {
            $country = $countries[$testCode];
        }
        else
        {
            if($countryName == null)
            {
                continue;
            }

            $country = \Model\Country::createCountry($continent, $countryName, $countryCode);
            $countries[$testCode] = $country;
        }

        // check if subdivision exists
        $testCode = $continentCode . "_" . $countryCode . "_" . $subdivision1Code;
        if (isset($subdivisions[$testCode]))
        {
            $subdivision = $subdivisions[$testCode];
        }
        else
        {
            if($subdivision1Name != null)
            {
                $subdivision = \Model\Subdivision::createSubdivision($continent, $country, $subdivision1Name, $subdivision1Code);
                $subdivisions[$testCode] = $subdivision;
            }
        }

        // check if city exists
        $testCode = $continentCode . "_" . $countryCode . "_" . $subdivision1Code . "_" . $cityName;
        if (isset($cities[$testCode]))
        {
            $city = $cities[$testCode];
        }
        else
        {
            if ($cityName != null) {
                $testCode = $countryCode . "_" . $cityName;

                $latitude = -1;
                $longitude = -1;

                if (isset($cityLatLons[$testCode])) {
                    $latitude = $cityLatLons[$testCode]['lat'];
                    $longitude = $cityLatLons[$testCode]['lon'];
                }

                $city = \Model\City::createCity($continent, $country, $subdivision, $cityName, $latitude, $longitude);
                $cities[$testCode] = $city;
            }
        }
    }

    fclose($handle);
}

echo "Done importing locations\n";

