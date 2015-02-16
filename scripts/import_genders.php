<?php
echo "Importing gender tags\n";

$db = \System\Mysql::getInstance();
$db->truncate('gender');

if (($handle = fopen("../database/genders.csv", "r")) !== FALSE)
{
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $data = array_map("utf8_encode", $data);

        // insert into mysql
        $genderTag = \Model\Gender::createGender($data[0]);
    }

    fclose($handle);
}

echo "Done importing gender tags\n";
