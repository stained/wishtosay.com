<?php namespace Model;

use \System\Mysql;
use \Util\String;

class PostTag extends root {

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $postId;

    /**
     * @var int
     */
    protected $tagId;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var Root
     */
    private $tag;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Post
     */
    public function getPost()
    {
        return Post::getById($this->postId);
    }

    /**
     * @return Root
     */
    public function getTag()
    {
        if (!empty($this->tag))
        {
            return $this->tag;
        }

        switch ($this->type)
        {
            case 'continent':
                $tag = Continent::getById($this->tagId);
                break;

            case 'country':
                $tag = Country::getById($this->tagId);
                break;

            case 'subdivision':
                $tag = SubDivision::getById($this->tagId);
                break;

            case 'city':
                return City::getById($this->tagId);
                break;

            case 'gender':
                $tag = Gender::getById($this->tagId);
                break;

            case 'tag':
                $tag = Tag::getById($this->tagId);
                break;
        }

        $this->tag = $tag;
        return $this->tag;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public static function getRandom()
    {
        $db = Mysql::getInstance();

        $query = 'SELECT pt.*, p.ageFrom, p.ageTo FROM posttag pt '.
                 'RIGHT JOIN (SELECT p.id AS id, p.ageFrom AS ageFrom, p.ageTo AS ageTo ' .
                 'FROM post p ORDER BY RAND() LIMIT 1) p ON (p.id = pt.postId)';

        $data = $db->query($query);

        $rows = array();
        $ageFrom = 0;
        $ageTo = 100;

        while ($row = $data->fetch_one())
        {
            $ageFrom = $row['ageFrom'];
            $ageTo = $row['ageTo'];

            if (!empty($row['id']))
            {
                $rows[] = static::init($row);
            }
        }

        return array($ageFrom, $ageTo, $rows);
    }

    /**
     * @param array $data
     * @return PostTag
     */
    private static function init($data)
    {
        if (empty($data))
        {
            return null;
        }

        $item = new self;
        $item->id = $data['id'];
        $item->postId = $data['postId'];
        $item->tagId = $data['tagId'];
        $item->type = $data['type'];
        return $item;
    }

    /**
     * @return PostTag[]
     */
    public static function getAllForPost($post)
    {
        $db = Mysql::getInstance();
        $data = $db->select('posttag', '`postId` = :postId', array('postId' => $post->getId()));

        $rows = array();

        while ($row = $data->fetch_one())
        {
            $rows[] = static::init($row);
        }

        return $rows;
    }

    /**
     * Get posttag by id
     *
     * @param int $id
     * @return PostTag|null
     */
    public static function getById($id)
    {
        $db = Mysql::getInstance();
        $data = $db->select('posttag', '`id` = :id', array('id' => $id));
        return static::init($data->fetch_one());
    }

    /**
     * @param Post $post
     * @param Root $tag
     * @return bool|PostTag
     */
    public static function createPostTag($post, $tag)
    {
        $item = new self;
        $item->postId = $post->getId();
        $item->tagId = $tag->getId();
        $item->type = $tag->getType();

        if ($item->create())
        {
            return $item;
        }

        return false;
    }

    protected function create()
    {
        $db = Mysql::getInstance();

        if ($db->insert('posttag', array(
            'postId' => $this->postId,
            'tagId' => $this->tagId,
            'type' => $this->type
        )))
        {
            $this->id = $db->insert_id();
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function toJsonArray()
    {
        $tag = $this->getTag();

        if (!empty($tag))
        {
            return $tag->toJsonArray();
        }
    }

    public function update()
    {
    }

    public function delete()
    {
    }

    public function toSearchBody(){
    }
}
