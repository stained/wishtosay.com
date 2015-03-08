<?php

namespace Controller;

use \Elasticsearch\Client;
use \System\Config;

use \Model\Continent;
use \Model\Country;
use \Model\SubDivision;
use \Model\City;
use \Model\Gender;
use \Model\Tag;

use \Util\String;

class Search extends Root {

    /**
     * @var Client
     */
    private static $client;

    /**
     * @var string
     */
    private static $index;


    /**
     * Basic search across all relevant fields
     *
     * @param array $parameters
     * @return \System\View
     */
    public static function base($parameters = array()) {
    }

    /**
     * Auto Complete
     *
     * @param array $parameters
     * @return \System\View
     */
    public static function autoComplete($parameters = array()) {

        if (empty($parameters))
        {
            return static::toJson(array(), 200);
        }

        $query = String::replaceForSearch(trim($parameters[0]));

        if (empty($query))
        {
            return static::toJson(array(), 400);
        }

        $params = array(
            'ignore'=>array(404, 400),
            'index'=>static::getIndex(),
            'type'=>array('gender', 'continent', 'country', 'subdivision', 'city', 'tag'),
            'body'=>array(
                'query'=>array('fuzzy_like_this'=>array(
                    'fields'=>array('search'),
                    'like_text' => $query
                )),
                'sort'=>array(array('search_weight'=>'desc'), '_score')
            )
        );

        $client = static::getClient();
        $results = $client->search($params);
        $hits = static::parseResults($results);

        if (empty($hits))
        {
            return static::toJson(array(), 200);
        }

        $response = array();

        foreach ($hits as $hit)
        {
            $class = 'tag-';

            switch ($hit['_type'])
            {
                case 'gender':
                    $class .= 'gender';
                    break;

                case 'location':
                case 'continent':
                case 'country':
                case 'subdivision':
                case 'city':
                    $class .= 'location';
                    break;

                default:
                    $class = '';
            }

            $response[] = array('id'=>$hit['_id'],
                                'text'=>utf8_decode($hit['_source']['tag']),
                                'type'=>$hit['_type'],
                                'class'=>$class
            );
        }

        return static::toJson($response, 200);
    }

    /**
     * @return Client
     */
    private static function getClient()
    {
        if (!empty(static::$client))
        {
            return static::$client;
        }

        $elasticSearchConfig = Config::get('elasticsearch');
        $params = array();
        $params['hosts'] = $elasticSearchConfig['hosts'];
        static::$client = new Client($params);

        return static::$client;
    }

    /**
     * @return string
     */
    private static function getIndex()
    {
        if (!empty(static::$index))
        {
            return static::$index;
        }

        $elasticSearchConfig = Config::get('elasticsearch');
        static::$index = $elasticSearchConfig['index'];
        return static::$index;
    }

    /**
     * Perform a complete re-indexing of the data
     */
    public static function reIndex()
    {
        $client = static::getClient();

        $params = array('index'=>static::getIndex(), 'ignore'=>array(404, 400));
        $client->indices()->delete($params);
        $client->indices()->create($params);

        static::createSearchArray(Continent::getAll());
        static::createSearchArray(Country::getAll());
        static::createSearchArray(SubDivision::getAll());
        static::createSearchArray(City::getAll());
        static::createSearchArray(Gender::getAll());
        static::createSearchArray(Tag::getAll());
    }

    /**
     * @param array $array
     */
    private static function createSearchArray($array)
    {
        if (count($array) > 0)
        {
            foreach ($array as $item)
            {
                static::createItem($item);
            }
        }
    }

    /**
     * @param \Model\Root $item
     */
    private static function createItem($item)
    {
        $client = static::getClient();

        $params = array(
            'body'=>$item->toSearchBody(),
            'index'=>static::getIndex(),
            'type'=>$item->getType(),
            'id'=>$item->getId(),
            'timestamp'=>time()
        );

        $client->index($params);
    }

    /**
     * @param \Model\Root $item
     */
    private static function updateItem($item)
    {
        // we could use partial updates here, but it adds a new level of complexity
        // so instead we just do what elasticsearch would do in any case and replace the item
        static::deleteItem($item);
        static::createItem($item);
    }

    /**
     * @param \Model\Root $item
     */
    private static function deleteItem($item)
    {
        $client = static::getClient();

        $params = array(
            'ignore'=>array(404, 400),
            'index'=>static::getIndex(),
            'type'=>static::$item->getType(),
            'id'=>$item->getId()
        );

        $client->delete($params);
    }

    /**
     * @param array $results
     * @return array|null
     */
    private static function parseResults($results)
    {
        $count = 0;
        $parsedResults = array();

        if (isset($results['hits']) && isset($results['hits']['total']))
        {
            $count = $results['hits']['total'];

            if ($count > 0 && !empty($results['hits']['hits']))
            {
                return $results['hits']['hits'];
            }
        }
    }

}