<?php
echo "Importing tags\n";

$db = \System\Mysql::getInstance();
$db->truncate('tag');

if (($handle = fopen("../database/tags.csv", "r")) !== FALSE)
{
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $data = array_map("utf8_encode", $data);

        // insert into mysql
        $genderTag = \Model\Tag::createTag($data[0]);
    }

    fclose($handle);
}

echo "Done importing tags\n";
