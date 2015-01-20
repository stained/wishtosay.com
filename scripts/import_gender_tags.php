<?php
echo "Importing gender tags\n";

$db = \System\Mysql::getInstance();
$db->truncate('gendertag');

if (($handle = fopen("../database/genders.csv", "r")) !== FALSE)
{
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $data = array_map("utf8_encode", $data);

        // insert into mysql
        $genderTag = \Model\GenderTag::createGenderTag($data[0]);

        if(!empty($genderTag)) {

            $searchBody = array(
                'tag' => $data[0]
            );

            // insert into elastic search
            $params = array(
                'body' => $searchBody,
                'index' => $searchIndex,
                'type' => 'gendertag',
                'id' => $genderTag->getId(),
                'timestamp' => time()
            );

            $client->index($params);
        }
    }

    fclose($handle);
}

echo "Done importing gender tags\n";
