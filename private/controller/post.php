<?php

namespace Controller;

use \Elasticsearch\Client;
use Model\Posttag;
use \System\Config;

use \Model\Continent;
use \Model\Country;
use \Model\Subdivision;
use \Model\City;
use \Model\Gender;
use \Model\Tag;

use \Util\String;
use \Util\Arr;

class Post extends Root {


    /**
     * @param array $parameters
     * @return \System\View
     */
    public static function base($parameters = array()) {
    }

    /**
     * Load
     *
     * @param array $parameters
     * @return \System\View
     */
    public static function load($parameters = array())
    {
        if (empty($parameters))
        {
            return static::toJson(array('c'=>0));
        }

        $decoded = json_decode(base64_decode($parameters[0]), true);

        if(empty($decoded) || !is_array($decoded))
        {
            return static::toJson(array('c'=>0));
        }

        // check for age
        $age = Arr::get($decoded, 'a', array('f'=>0, 't'=>100));

        if (empty($age['f']) || empty($age['t']) ||
            !is_numeric($age['f']) || !is_numeric($age['t']) ||
            $age['t'] < $age['f']
        )
        {
            $age = array('f'=>0, 't'=>100);
        }

        $tags = Arr::get($decoded, 't', array());

        $searchTags = array();

        if (!empty($tags))
        {
            $validTypes = array('continent', 'country', 'subdivision', 'city', 'gender', 'tag');

            foreach($tags as $tag)
            {
                // check for required values
                $value = trim(Arr::get($tag, 'te', ''));
                $type = Arr::get($tag, 'ty', 'tag');
                $id = Arr::get($tag, 'i', 0);

                if (empty($value) || empty($type) || $id == 0 || !in_array($type, $validTypes))
                {
                    continue;
                }

                $searchTags[] = $tag;
            }

            if (empty($searchTags))
            {
                return static::toJson(array('c'=>0));
            }
        }

        // search for any posts within that range and with tags
        $posts = \Model\Post::getAllForTagsAndAgeRange($searchTags, $age['f'], $age['t']);

        if(empty($posts))
        {
            return static::toJson(array('c'=>0));
        }

        foreach ($posts as &$post)
        {
            $post = $post->toJsonArray();
        }

        return static::toJson(array('c'=>count($posts), 'p'=>$posts));
    }

    /**
     * Create
     *
     * @param array $parameters
     * @return \System\View
     */
    public static function create($parameters = array()) {

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data))
        {
            $response[] = array('error'=>'Invalid Post');
            return static::toJson($response, 400);
        }

        // check for required fields
        $text = trim(String::clean(Arr::get($data, 'te', '')));

        if (empty($text)) {
            $response[] = array('error'=>'Invalid Post Text');
            return static::toJson($response, 400);
        }

        // check for age
        $age = Arr::get($data, 'a', array('f'=>0, 't'=>100));

        if (empty($age['f']) || empty($age['t']) ||
            !is_numeric($age['f']) || !is_numeric($age['t']) ||
            $age['t'] < $age['f']
        )
        {
            $response[] = array('error'=>'Invalid Age Range');
            return static::toJson($response, 400);
        }

        // create post
        $post = \Model\Post::createPost($text, $age['f'], $age['t']);

        if (empty($post))
        {
            $response[] = array('error'=>'Something went wrong, please try again.');
            return static::toJson($response, 500);
        }

        // and tags
        $tags = Arr::get($data, 't', array());

        // get all tags and create new ones if necessary
        $resolvedTags = static::getTags($tags, true);

        // create post tags
        if (!empty($resolvedTags))
        {
            $postTags = array();

            foreach ($resolvedTags as $tag)
            {
                $postTag = Posttag::createPostTag($post, $tag);

                if (!empty($postTag))
                {
                    $postTags[] = $postTag;
                }
            }

            if (!empty($postTags))
            {
                $post->setTags($postTags);
            }
        }

        $response[] = $post->toJsonArray();
        return static::toJson($response, 200);
    }

    /**
     * @param array $tags
     * @param bool $create
     * @return Root[]|array
     */
    private static function getTags($tags, $create = false)
    {
        $resolvedTags = array();

        if (!empty($tags))
        {
            // maybe optimize this one day by bunching tag requests of the same type
            foreach ($tags as $tag)
            {
                // check for required values
                $value = trim(Arr::get($tag, 'te', ''));

                if (empty($value))
                {
                    continue;
                }

                $id = Arr::get($tag, 'i', 0);
                $type = Arr::get($tag, 'ty', 'tag');

                // check if tag exists
                if (is_numeric($id) && $id > 0)
                {
                    switch ($type) {
                        case 'continent':
                            $tag = Continent::getById($id);
                            break;

                        case 'country':
                            $tag = Country::getById($id);
                            break;

                        case 'subdivision':
                            $tag = Subdivision::getById($id);
                            break;

                        case 'gender';
                            $tag = Gender::getById($id);
                            break;

                        default:
                            $tag = Tag::getById($id);
                            break;
                    }
                }
                else
                {
                    // check if tag exists by value
                    $tag = Tag::getByValue($value);

                    if (empty($tag) && $create)
                    {
                        // create new tag
                        $tag = Tag::createTag($value);
                    }
                }

                if (!empty($tag))
                {
                    $resolvedTags[] = $tag;
                }
            }
        }

        return $resolvedTags;
    }

}