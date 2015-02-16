<?php namespace Model;

use \System\Mysql;
use \Util\String;

class Tag extends root {

    const SEARCH_WEIGHT = 6;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $tag;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param array $data
     * @return Tag
     */
    private static function init($data)
    {
        if (empty($data))
        {
            return null;
        }

        $item = new self;
        $item->id = $data['id'];
        $item->tag = $data['tag'];
        return $item;
    }

    /**
     * @return Tag[]
     */
    public static function getAll()
    {
        $db = Mysql::getInstance();
        $data = $db->select('tag');

        $rows = array();

        while ($row = $data->fetch_one())
        {
            $rows[] = static::init($row);
        }

        return $rows;
    }

    /**
     * Get tag by id
     *
     * @param int $id
     * @return Tag|null
     */
    public static function getById($id)
    {
        $db = Mysql::getInstance();
        $data = $db->select('tag', '`id` = :id', array('id' => $id));
        return static::init($data->fetch_one());
    }

    /**
     * Get tag by value
     *
     * @param string $value
     * @return Tag|null
     */
    public static function getByValue($value)
    {
        $db = Mysql::getInstance();
        $data = $db->select('tag', '`tag` = :tag', array('tag' => $value));
        return static::init($data->fetch_one());
    }

    /**
     * @param string $tag
     * @return bool|Tag
     */
    public static function createTag($tag)
    {
        $item = new self;
        $item->tag = $tag;

        if ($item->create())
        {
            return $item;
        }

        return false;
    }

    protected function create()
    {
        $db = Mysql::getInstance();

        if ($db->insert('tag', array(
            'tag' => $this->tag
        )))
        {
            $this->id = $db->insert_id();
            return true;
        }

        return false;
    }

    public function update()
    {
    }

    public function delete()
    {
    }

    /**
     * @return array
     */
    public function toJsonArray()
    {
        return array(
            'i'=>$this->id,
            'ty'=>'tag',
            'te'=>$this->getTag()
        );
    }

    /**
     * @return array
     */
    public function toSearchBody(){
        return array(
            'search' => String::replaceForSearch($this->getTag()),
            'search_weight' => static::SEARCH_WEIGHT,
            'tag' => $this->getTag()
        );
    }
}
