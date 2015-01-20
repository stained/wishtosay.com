<?php namespace Model;

use \System\Mysql;

class GenderTag extends root {

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
     * @return GenderTag
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
     * Get gender tag by id
     *
     * @param int $id
     * @return GenderTag|null
     */
    public static function getById($id)
    {
        $db = Mysql::getInstance();
        $data = $db->select('gendertag', '`id` = :id', array('id' => $id));
        return static::init($data->fetch_one());
    }

    /**
     * @param string $tag
     * @return GenderTag
     */
    public static function createGenderTag($tag)
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

        if ($db->insert('gendertag', array(
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

}
