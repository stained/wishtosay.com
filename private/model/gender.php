<?php namespace Model;

use \System\Mysql;
use \Util\String;

class Gender extends root {

    const SEARCH_WEIGHT = 5;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $gender;

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
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param array $data
     * @return Gender
     */
    private static function init($data)
    {
        if (empty($data))
        {
            return null;
        }

        $item = new self;
        $item->id = $data['id'];
        $item->gender = $data['gender'];
        return $item;
    }

    /**
     * @return Gender[]
     */
    public static function getAll()
    {
        $db = Mysql::getInstance();
        $data = $db->select('gender');

        $rows = array();

        while ($row = $data->fetch_one())
        {
            $rows[] = static::init($row);
        }

        return $rows;
    }

    /**
     * Get gender by id
     *
     * @param int $id
     * @return Gender|null
     */
    public static function getById($id)
    {
        $db = Mysql::getInstance();
        $data = $db->select('gender', '`id` = :id', array('id' => $id));
        return static::init($data->fetch_one());
    }

    /**
     * @param string $gender
     * @return bool|Gender
     */
    public static function createGender($gender)
    {
        $item = new self;
        $item->gender = $gender;

        if ($item->create())
        {
            return $item;
        }

        return false;
    }

    protected function create()
    {
        $db = Mysql::getInstance();

        if ($db->insert('gender', array(
            'gender' => $this->gender
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
            'ty'=>'gender',
            'te'=>$this->getGender()
        );
    }

    /**
     * @return array
     */
    public function toSearchBody(){
        return array(
            'search' => String::replaceForSearch($this->getGender()),
            'search_weight' => static::SEARCH_WEIGHT,
            'tag' => $this->getGender()
        );
    }
}
